<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model;

use Joomla\DI\Container as JoomlaContainer;
use Joomla\DI\ContainerAwareInterface;
use Windwalker\DI\Container;

/**
 * Windwalker basic model class.
 *
 * @since 2.0
 */
class Model extends \JModelDatabase implements ContainerAwareInterface
{
	/**
	 * The model (base) name
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = null;

	/**
	 * The URL option for the component.
	 *
	 * @var  string
	 */
	protected $option = null;

	/**
	 * Context string for the model type.
	 *
	 * This is used to handle uniqueness when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var  string
	 */
	protected $context = '';

	/**
	 * The event to trigger when cleaning cache.
	 *
	 * @var string
	 */
	protected $eventCleanCache = null;

	/**
	 * The DI Container.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Constructor
	 *
	 * @param   array              $config    An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   JoomlaContainer    $container Service container.
	 * @param   \JRegistry         $state     The model state.
	 * @param   \JDatabaseDriver   $db        The database adapter.
	 *
	 * @throws \Exception
	 */
	public function __construct($config = array(), JoomlaContainer $container = null, \JRegistry $state = null, \JDatabaseDriver $db = null)
	{
		// Guess the option from the class name (Option)Model(View).
		if (empty($this->prefix))
		{
			$r = null;

			if (!preg_match('/(.*)Model/i', get_class($this), $r))
			{
				throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}

			$this->prefix = strtolower($r[1]);
		}

		$this->option = 'com_' . $this->prefix;

		// Guess name
		$this->name = $this->name ? : \JArrayHelper::getValue($config, 'name', $this->getName());

		// Register the paths for the form
		$this->registerTablePaths($config);

		// Set the clean cache event
		$this->eventCleanCache = $this->eventCleanCache ? : \JArrayHelper::getValue($config, 'event_clean_cache', 'onContentCleanCache');

		$this->container = $container ? : $this->getContainer();

		$state = new \JRegistry($config);

		parent::__construct($state, $db);

		// Guess the context as Option.ModelName.
		$this->context = $this->context ? : strtolower($this->option . '.' . $this->getName());

		// Set the internal state marker - used to ignore setting state from the request
		if (empty($config['ignore_request']))
		{
			// Protected method to auto-populate the model state.
			$this->populateState();
		}
	}

	/**
	 * Method to get the model name
	 *
	 * The model name. By default parsed using the classname or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @throws  \Exception
	 * @return  string  The name of the model
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$r = null;

			if (!preg_match('/Model(.*)/i', get_class($this), $r))
			{
				throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}

			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @throws  \Exception
	 * @return  \JTable  A JTable object
	 */
	public function getTable($name = '', $prefix = '', $options = array())
	{
		if (empty($name))
		{
			$name = $this->getName();
		}

		if (empty($prefix))
		{
			$prefix = ucfirst($this->prefix) . 'Table';
		}

		if ($table = $this->createTable(ucfirst($name), $prefix, $options))
		{
			return $table;
		}

		throw new \Exception(\JText::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
	}

	/**
	 * Method to register paths for tables
	 *
	 * @param array $config
	 *
	 * @return  object  The property where specified, the state object where omitted
	 */
	public function registerTablePaths($config = array())
	{
		// Set the default view search path
		if (array_key_exists('table_path', $config))
		{
			$this->addTablePath($config['table_path']);
		}
		elseif (defined('JPATH_COMPONENT_ADMINISTRATOR'))
		{
			// Register the paths for the form
			$paths = new \SplPriorityQueue;
			$paths->insert(JPATH_COMPONENT_ADMINISTRATOR . '/table', 'normal');

			// For legacy purposes. Remove for 4.0
			$paths->insert(JPATH_COMPONENT_ADMINISTRATOR . '/tables', 'normal');

			foreach (clone $paths as $path)
			{
				$this->addTablePath($path);
			}
		}
	}

	/**
	 * Set basic name.
	 *
	 * @param string $name THe model name.
	 *
	 * @return Model Return self to support chaining.
	 */
	public function setName($name)
	{
		$this->name = strtolower($name);

		return $this;
	}

	/**
	 * Set component option name.
	 *
	 * @param string $option The component option name.
	 *
	 * @return  Model Return self to support chaining.
	 */
	public function setOption($option)
	{
		$this->option = $option;

		$this->prefix = substr($this->option, 4);

		return $this;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method will only called in constructor. Using `ignore_request` to ignore this method.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		$this->loadState();
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return false;
			}

			$user = $this->container->get('user');

			return $user->authorise('core.delete', $this->option);
		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record)
	{
		$user = Container::getInstance()->get('user');

		return $user->authorise('core.edit.state', $this->option);
	}

	/**
	 * Method to load and return a model object.
	 *
	 * @param   string  $name    The name of the view
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration settings to pass to JTable::getInstance
	 *
	 * @return  mixed  Model object or boolean false if failed
	 *
	 * @see     JTable::getInstance()
	 */
	protected function createTable($name, $prefix = 'Table', $config = array())
	{
		// Clean the model name
		$name   = preg_replace('/[^A-Z0-9_]/i', '', $name);
		$prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);

		// Make sure we are returning a DBO object
		if (!array_key_exists('dbo', $config))
		{
			$config['dbo'] = $this->getDb();
		}

		return \JTable::getInstance($name, $prefix, $config);
	}

	/**
	 * Adds to the stack of model table paths in LIFO order.
	 *
	 * @param   mixed  $path  The directory as a string or directories as an array to add.
	 *
	 * @return  void
	 */
	public static function addTablePath($path)
	{
		\JTable::addIncludePath($path);
	}

	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		$conf = $this->container->get('joomla.config');
		$dispatcher = $this->container->get('event.dispatcher');
		$input = $this->container->get('input');

		$options = array(
			'defaultgroup' => ($group)     ? $group : (isset($this->option) ? $this->option : $input->get('option')),
			'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache')
		);

		$cache = \JCache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$dispatcher->trigger($this->eventCleanCache, $options);
	}

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		if (!$this->container)
		{
			$this->container = Container::getInstance($this->option);
		}

		return $this->container;
	}

	/**
	 * Set the DI container.
	 *
	 * @param   JoomlaContainer $container The DI container.
	 *
	 * @return  Model Return self to support chaining.
	 */
	public function setContainer(JoomlaContainer $container)
	{
		$this->container = $container;

		return $this;
	}
}
