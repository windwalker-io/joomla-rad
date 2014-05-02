<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller\Resolver;

use JInput;
use Windwalker\DI\Container;
use Windwalker\Controller\Controller;

defined('_JEXEC') or die('Restricted access');

/**
 * Resolve the task name to get controller.
 *
 * @since 2.0
 */
class ControllerResolver
{
	/**
	 * Task mapper.
	 *
	 * @var  array
	 */
	protected $taskMapper = array();

	/**
	 * Application object.
	 *
	 * @var  \JApplicationCms
	 */
	protected $application;

	/**
	 * The DI Container.
	 *
	 * @var  \Joomla\DI\Container
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @param \JApplicationCms $application  The application object.
	 * @param Container        $container    Th DI Container.
	 */
	public function __construct(\JApplicationCms $application, Container $container)
	{
		$this->container   = $container;
		$this->application = $application;
	}

	/**
	 * Method to parse a controller from task string.
	 *
	 * @param   string  $prefix The class name prefix
	 * @param   string  $task   The key to get controller.
	 * @param   JInput  $input  Input request.
	 *
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @throws \Exception
	 *
	 * @return  Controller  A JController object
	 */
	public function getController($prefix, $task, $input)
	{
		if (!trim($task))
		{
			throw new \InvalidArgumentException('No task.');
		}

		// Toolbar expects old style but we are using new style
		// Remove when toolbar can handle either directly
		if (strpos($task, '/') !== false)
		{
			$tasks = explode('/', $task);
		}
		else
		{
			$tasks = explode('.', $task);
		}

		if (!count($tasks) || empty($tasks[0]))
		{
			$tasks = array('Display');
		}

		$tasks = array_map('ucfirst', $tasks);

		$name = '';

		if (count($tasks) > 1)
		{
			$name = array_shift($tasks);
		}

		$controllerName = $this->resolveController($prefix, $name, implode('.', $tasks));

		// Config
		$config = array(
			'prefix' => strtolower($prefix),
			'name'   => strtolower($name),
			'task'   => strtolower(implode('.', $tasks)),
			'option' => 'com_' . strtolower($prefix)
		);

		// Using delegator to create controller.
		$delegator = $this->getDelegator($config);

		/** @var $controller Controller */
		$controller = $delegator->getController($controllerName, $input, $this->application, $config);

		return $controller;
	}

	/**
	 * Resolve the controller name.
	 *
	 * @param string $prefix Component name prefix.
	 * @param string $name   Controller name.
	 * @param string $task   The task.
	 *
	 * @return  string Controller class.
	 *
	 * @throws \RuntimeException
	 * @throws \Exception
	 */
	public function resolveController($prefix, $name, $task)
	{
		$key = strtolower($name . '.' . $task);

		if (!empty($this->taskMapper[$key]))
		{
			return $this->taskMapper[$key];
		}

		$controllerName = ucfirst($prefix) . 'Controller' . $name . str_replace('.', '', $task);

		if (!class_exists($controllerName))
		{
			$controllerName = '\\Windwalker\\Controller\\' . str_replace('.', '\\', $task) . 'Controller';

			if (!class_exists($controllerName))
			{
				if (JDEBUG)
				{
					throw new \RuntimeException(sprintf('Controller %s not found.', $controllerName));
				}
				else
				{
					throw new \Exception('Bad Route.', 404);
				}
			}
		}

		return $controllerName;
	}

	/**
	 * Register (map) a task to a method in the class.
	 *
	 * @param   string  $task        The task.
	 * @param   string  $controller  The name of the controller in the derived class to perform for this task.
	 *
	 * @return  ControllerResolver  A JControllerLegacy object to support chaining.
	 */
	public function registerTask($task, $controller)
	{
		$this->taskMapper[strtolower($task)] = $controller;

		return $this;
	}

	/**
	 * Unregister (unmap) a task in the class.
	 *
	 * @param   string  $task  The task.
	 *
	 * @return  ControllerResolver  This object to support chaining.
	 */
	public function unregisterTask($task)
	{
		unset($this->taskMapper[strtolower($task)]);

		return $this;
	}

	/**
	 * Get delegator object to dispatch controller task.
	 *
	 * You can override it in component controller folders, otherwise Windwalker will use the default delegator.
	 *
	 * @param array $config  Controller config.
	 *
	 * @return  ControllerDelegator  Delegator object.
	 *
	 * @throws \LogicException
	 */
	public function getDelegator($config)
	{
		$defaultDelegator = '\\Windwalker\\Controller\\Resolver\\ControllerDelegator';

		$key = $config['prefix'] . '.' . $config['name'] . '.controller.delegator';

		try
		{
			// Find from container
			$delegator = $this->container->get($key);
		}
		catch (\InvalidArgumentException $e)
		{
			// Find from component.
			$class = ucfirst($config['prefix']) . 'Controller' . ucfirst($config['name']) . 'Delegator';

			if (class_exists($class))
			{
				if (!is_subclass_of($class, $defaultDelegator))
				{
					throw new \LogicException(sprintf('%s should extends from %', $class, $defaultDelegator));
				}

				$delegator = new $class;
			}
			else
			{
				// Find from windwalker
				$delegator = new $defaultDelegator;
			}

			$this->container->alias($key, $class)
				->share($class, $delegator);
		}

		return $delegator;
	}
}
