<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Component;

use Windwalker\Controller\Controller;
use Windwalker\DI\Container;
use Windwalker\Event\ListenerHelper;

/**
 * Component class.
 *
 * @since 2.0
 */
class Component
{
	/**
	 * Joomla Application object.
	 *
	 * @var \JApplicationCms
	 */
	protected $application;

	/**
	 * DI Container.
	 *
	 * @var \Joomla\DI\Container
	 */
	protected $container;

	/**
	 * Input object.
	 *
	 * @var \JInput
	 */
	protected $input;

	/**
	 * Component name without `com_`.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Component option name. Example `com_flower`.
	 *
	 * @var  string
	 */
	protected $option;

	/**
	 * Reflection of this class.
	 *
	 * @var \ReflectionClass
	 */
	protected $reflection;

	/**
	 * Default task name.
	 *
	 * @var string
	 */
	protected $defaultController;

	/**
	 * The paths of this component.
	 *
	 * @var array
	 */
	protected $path = array(
		'self',
		'site',
		'administrator'
	);

	/**
	 * Constructor.
	 *
	 * @param string           $name        The Component name.
	 * @param \JInput          $input       The Input object.
	 * @param \JApplicationCms $application The Application object.
	 * @param Container        $container   The DI container.
	 *
	 * @throws \Exception
	 */
	public function __construct($name = null, $input = null, $application = null, $container = null)
	{
		$this->application = $application ?: \JFactory::getApplication();
		$this->input       = $input       ?: $this->application->input;
		$this->name        = $name;

		// Guess component name.
		if (!$this->name)
		{
			$reflection = $this->getReflection();

			$this->name = $reflection->getShortName();

			$this->name = str_replace('Component', '', $this->name);

			if (!$this->name)
			{
				throw new \Exception('Component need name.');
			}
		}

		$this->option = 'com_' . strtolower($this->name);

		$this->container = $container ?: Container::getInstance($this->option);

		$this->init();
	}

	/**
	 * Execute this component.
	 *
	 * @return mixed The return value of this component.
	 */
	public function execute()
	{
		$dispatcher = $this->container->get('event.dispatcher');

		$this->loadConfiguration();

		$this->prepare();

		// Event
		$dispatcher->trigger('onComponentBeforeExecute', array($this->name, $this, $this->input));

		$result = $this->doExecute();

		// Event
		$dispatcher->trigger('onComponentAfterExecute', array($this->name, $this, $this->input, $result));

		$result = $this->postExecute($result);

		return $result;
	}

	/**
	 * Do execute.
	 *
	 * @return mixed The return value of this component.
	 */
	protected function doExecute()
	{
		$task = $this->input->get('task', $this->input->get('controller'));

		/** @var $controller Controller */
		$resolver   = $this->container->get('controller.resolver');
		$controller = $resolver->getController($this->name, $task, $this->input);

		$controller->setComponentPath(JPATH_BASE . '/components/' . $this->option);

		return $controller->setContainer($this->container)
			->execute();
	}

	/**
	 * Post execute hook.
	 *
	 * @param mixed $result The return value of this component.
	 *
	 * @return  mixed  The return value of this component.
	 */
	protected function postExecute($result)
	{
		return $result;
	}

	/**
	 * Prepare hook of this component.
	 *
	 * Do some customize initialise through extending this method.
	 *
	 * @return void
	 */
	public function prepare()
	{
	}

	/**
	 * Init this component.
	 *
	 * @return void
	 */
	public function init()
	{
		$dispatcher = $this->container->get('event.dispatcher');

		// Event
		$dispatcher->trigger('onComponentBeforeInit', array($this->name, $this, $this->input));

		// We build component path constant, helpe us get path easily.
		$this->path['self']          = JPATH_BASE . '/components/' . strtolower($this->option);
		$this->path['site']          = JPATH_ROOT . '/components/' . strtolower($this->option);
		$this->path['administrator'] = JPATH_ROOT . '/administrator/components/' . strtolower($this->option);

		define(strtoupper($this->name) . '_SELF',  $this->path['self']);
		define(strtoupper($this->name) . '_SITE',  $this->path['site']);
		define(strtoupper($this->name) . '_ADMIN', $this->path['administrator']);

		// Register some useful object for this component.
		$this->container->registerServiceProvider(new ComponentProvider($this->name, $this));

		$task       = $this->input->getWord('task');
		$controller = $this->input->getWord('controller');

		// Prepare default controller
		if (!$task && !$controller)
		{
			// If we got view, set it to display controller.
			$view = $this->input->get('view');
			$task = $view ? $view . '.display' : $this->defaultController;

			$this->input->set('task',       $task);
			$this->input->set('controller', $task);
		}

		// Register form and fields
		\JForm::addFieldPath(WINDWALKER_SOURCE . '/Form/Fields');
		\JForm::addFormPath(WINDWALKER_SOURCE . '/Form/Forms');

		$this->registerEventListener();

		// Register elFinder controllers
		// @TODO: Should use event listener
		$this->registerTask('finder.elfinder.display', '\\Windwalker\\Elfinder\\Controller\\DisplayController');
		$this->registerTask('finder.elfinder.connect', '\\Windwalker\\Elfinder\\Controller\\ConnectController');

		// Event
		$dispatcher->trigger('onComponentAfterInit', array($this->name, $this, $this->input));
	}

	/**
	 * Register EventListeners.
	 *
	 * @return  void
	 */
	protected function registerEventListener()
	{
		ListenerHelper::registerListeners(
			ucfirst($this->name),
			$this->container->get('event.dispatcher'),
			$this->path['administrator'] . '/src/' . ucfirst($this->name) . '/Listener'
		);
	}

	/**
	 * Register (map) a task to a method in the class.
	 *
	 * @param   string  $task        The task.
	 * @param   string  $controller  The name of the method in the derived class to perform for this task.
	 *
	 * @return  Component  A JControllerLegacy object to support chaining.
	 */
	public function registerTask($task, $controller)
	{
		$this->container->get('controller.resolver')->registerTask($task, $controller);

		return $this;
	}

	/**
	 * Unregister (unmap) a task in the class.
	 *
	 * @param   string  $task  The task.
	 *
	 * @return  Component  This object to support chaining.
	 */
	public function unregisterTask($task)
	{
		$this->container->get('controller.resolver')->unregisterTask($task);

		return $this;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $assetName   The asset name
	 * @param   integer  $categoryId  The category ID.
	 * @param   integer  $id          The item ID.
	 *
	 * @return  \Windwalker\Object\Object
	 */
	public function getActions($assetName, $categoryId = 0, $id = 0)
	{
		$user = $this->container->get('user');

		return ComponentHelper::getActions($user, $this->option, $assetName, $categoryId, $id);
	}

	/**
	 * Get the DI container.
	 *
	 * @return Container
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Set the Container.
	 *
	 * @param Container $container The DI Container.
	 *
	 * @return Component Return self to support chaining.
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * Get Application object.
	 *
	 * @return \JApplicationCms The Application object.
	 */
	public function getApplication()
	{
		return $this->application;
	}

	/**
	 * Set Application object.
	 *
	 * @param \JApplicationBase $application The Application object.
	 *
	 * @return Component Return self to support chaining.
	 */
	public function setApplication(\JApplicationBase $application)
	{
		$this->application = $application;

		return $this;
	}

	/**
	 * Get the Input object.
	 *
	 * @return \JInput The input object.
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Set Input object
	 *
	 * @param \JInput $input The Input object.
	 *
	 * @return Component Return self to support chaining.
	 */
	public function setInput(\JInput $input)
	{
		$this->input = $input;

		return $this;
	}

	/**
	 * Load configuration file. (Not used now.)
	 *
	 * @return void
	 */
	protected function loadConfiguration()
	{
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
	 * Get Default controller.
	 *
	 * @return string Default controller.
	 */
	public function getDefaultController()
	{
		return $this->defaultController;
	}

	/**
	 * Set Default controller.
	 *
	 * @param string $defaultController Default controller.
	 *
	 * @return Component Return self to support chaining.
	 */
	public function setDefaultController($defaultController)
	{
		$this->defaultController = $defaultController;

		return $this;
	}

	/**
	 * Get component path.
	 *
	 * @param string $client Site or administrator.
	 *
	 * @return string Path of this client.
	 */
	public function getPath($client = 'self')
	{
		$client = ($client == 'admin') ? 'administrator' : $client;

		return $this->path[$client];
	}

	/**
	 * Get site path. Alias of getPath().
	 *
	 * @return string Site path.
	 */
	public function getSitePath()
	{
		return $this->getPath('site');
	}

	/**
	 * Get admin path. Alias of getPath().
	 *
	 * @return string Admin path.
	 */
	public function getAdminPath()
	{
		return $this->getPath('administrator');
	}
}
