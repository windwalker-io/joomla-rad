<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller;

use JApplicationCms;
use JInput;
use Joomla\DI\Container as JoomlaContainer;
use Joomla\DI\ContainerAwareInterface;

use Windwalker\Model\Model;
use Windwalker\DI\Container;

/**
 * Class Controller
 *
 * @since 2.0
 */
abstract class Controller extends \JControllerBase implements ContainerAwareInterface
{
	/**
	 * The application object.
	 *
	 * @var    JApplicationCms
	 * @since  12.1
	 */
	protected $app = null;

	/**
	 * Prefix for the view and model classes
	 *
	 * @var  string
	 */
	protected $prefix = null;

	/**
	 * Property option.
	 *
	 * @var string
	 */
	protected $option = null;

	/**
	 * Property name.
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Property componentPath.
	 *
	 * @var string
	 */
	protected $componentPath = null;

	/**
	 * Property reflection.
	 *
	 * @var \ReflectionClass
	 */
	protected $reflection = null;

	/**
	 * Property task.
	 *
	 * @var string
	 */
	protected $task = '';

	/**
	 * Property container.
	 *
	 * @var JoomlaContainer
	 */
	protected $container;

	/**
	 * Instantiate the controller.
	 *
	 * @param   \JInput          $input  The input object.
	 * @param   \JApplicationCms $app    The application object.
	 * @param   array            $config The config object.
	 */
	public function __construct(JInput $input = null, JApplicationCms $app = null, $config = array())
	{
		if (!$this->prefix && !empty($config['prefix']))
		{
			$this->prefix = $config['prefix'];
		}

		if (!$this->option && !empty($config['option']))
		{
			$this->option = $config['option'];
		}

		if (!$this->name && !empty($config['name']))
		{
			$this->name = $config['name'];
		}

		if (!$this->task && !empty($config['task']))
		{
			$this->task = $config['task'];
		}

		parent::__construct($input, $app);
	}

	/**
	 * Prepare execute hook.
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
	}

	/**
	 * Execute the controller.
	 *
	 * @return  boolean  Executed result or rendered string.
	 */
	public function execute()
	{
		$this->prepareExecute();

		$return = $this->doExecute();

		return $this->postExecute($return);
	}

	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	abstract protected function doExecute();

	/**
	 * Pose execute hook.
	 *
	 * @param   mixed  $data  Executed return value.
	 *
	 * @return  mixed
	 */
	protected function postExecute($data = null)
	{
		return $data;
	}

	/**
	 * Fetch HMVC result.
	 *
	 * @param string       $prefix  The controller prefix, it means the component.
	 * @param string       $name    Controller task name.
	 * @param JInput|array $input   The input object or an array, it will pass to child controller.
	 *
	 * @return mixed HMVC executed result.
	 */
	public function fetch($prefix, $name, $input = array())
	{
		if (!($input instanceof JInput))
		{
			// Renew a input
			$newInput = new JInput($_REQUEST);

			foreach ($input as $field => $value)
			{
				$newInput->set($field, $value);
			}

			$input = $newInput;
		}

		$input->set('hmvc', true);

		/** @var $resolver \Windwalker\Controller\Resolver\ControllerResolver */
		$resolver   = $this->container->get('controller.resolver');
		$controller = $resolver->getController($prefix, $name, $input)
			->setComponentPath($this->componentPath)
			->setContainer($this->container);

		return $controller->execute();
	}

	/**
	 * Get Component path.
	 *
	 * @return string Component path.
	 */
	public function getComponentPath()
	{
		return $this->componentPath;
	}

	/**
	 * Set component path.
	 *
	 * @param   string   $componentPath  The component path.
	 *
	 * @return  Controller  Return self to support chaining.
	 */
	public function setComponentPath($componentPath)
	{
		$this->componentPath = $componentPath;

		return $this;
	}

	/**
	 * Get reflection and cache it.
	 *
	 * @return \ReflectionClass PHP Reflection object.
	 */
	public function getReflection()
	{
		if ($this->reflection)
		{
			return $this->reflection;
		}

		$this->reflection = new \ReflectionClass($this);

		return $this->reflection;
	}

	/**
	 * Get Prefix.
	 *
	 * @return string Prefix string.
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * Set Prefix.
	 *
	 * @param  string $prefix Prefix string.
	 *
	 * @return Controller  Return self to support chaining.
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;

		return $this;
	}

	/**
	 * Get controller name.
	 *
	 * @return  string
	 */
	public function getName()
	{
		if ($this->name !== null)
		{
			return $this->name;
		}

		$ref = $this->getReflection();

		$name = explode('Controller', $ref->getName());

		if ($name[0] == $this->getPrefix())
		{
			return $this->name = '';
		}
		elseif (!empty($name[1]))
		{
			return $this->name = trim($name[1], '\\');
		}

		return '';
	}

	/**
	 * Set controller name
	 *
	 * @param   string $name The controller name.
	 *
	 * @return  Controller  Return self to support chaining.
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Set option name.
	 *
	 * @param   string $option Option name.
	 *
	 * @return  Controller  Return self to support chaining.
	 */
	public function setOption($option)
	{
		$this->option = $option;

		return $this;
	}

	/**
	 * Get task name.
	 *
	 * @return  string
	 */
	public function getTask()
	{
		return $this->task;
	}

	/**
	 * Set task name.
	 *
	 * @param   string $task The task name.
	 *
	 * @return  Controller  Return self to support chaining.
	 */
	public function setTask($task)
	{
		$this->task = $task;

		return $this;
	}

	/**
	 * Check session token or die.
	 *
	 * @return void
	 */
	protected function checkToken()
	{
		// Check for request forgeries
		\JSession::checkToken() or jexit(\JText::_('JInvalid_Token'));
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name     The model name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $config   Configuration array for model. Optional.
	 * @param   boolean $forceNew Force get new model, or we get it from cache.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = null, $prefix = null, $config = array(), $forceNew = false)
	{
		// Get name.
		if (!$name)
		{
			$name = $this->getName();
		}

		// Get Prefix
		if (!$prefix)
		{
			$prefix = ucfirst($this->getPrefix());
		}

		$modelName = $prefix . 'Model' . ucfirst($name);

		if (!class_exists($modelName))
		{
			$modelName = '\\Windwalker\\Model\\Model';
		}

		$defaultConfig = array(
			'name'   => strtolower($name),
			'option' => strtolower($this->option),
			'prefix' => strtolower($this->getPrefix())
		);

		$config = array_merge($defaultConfig, $config);

		// Get model.
		$container = $this->getContainer();

		$modelKey = 'model.' . strtolower($name);

		try
		{
			$model = $container->get($modelKey, $forceNew);
		}
		catch (\InvalidArgumentException $e)
		{
			$container->share(
				$modelKey,
				function(Container $container) use($modelName, $config)
				{
					return new $modelName($config, $container, null, $container->get('db'));
				}
			);

			$model = $container->get($modelKey);
		}

		return $model;
	}

	/**
	 * Get the DI container.
	 *
	 * @return  JoomlaContainer
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
	 * @return  Controller  Return self to support chaining.
	 */
	public function setContainer(JoomlaContainer $container)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * Set message to queue.
	 *
	 * @param   string  $msg   Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param   string  $type  Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
	 *
	 * @return  Controller
	 */
	public function setMessage($msg, $type = 'message')
	{
		if (!$this->input->get('quiet', false))
		{
			$this->app->enqueueMessage($msg, $type);

			return $this;
		}

		return $this;
	}
}
