<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Router;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Menu\AbstractMenu;
use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Folder;
use Windwalker\Helper\PathHelper;
use Windwalker\Router\Handler\RouterHandlerInterface;

/**
 * The ComponentRouter class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ComponentViewRouter extends RouterView
{
	/**
	 * Property handlers.
	 *
	 * @var  RouterHandlerInterface[]
	 */
	protected $handlers = array();

	/**
	 * Class constructor.
	 *
	 * @param   CMSApplication $app  Application-object that the router should use
	 * @param   AbstractMenu   $menu Menu-object that the router should use
	 *
	 * @since   3.4
	 */
	public function __construct($app = null, $menu = null)
	{
		$this->registerHandlers();
		
		parent::__construct($app, $menu);
	}

	/**
	 * registerViews
	 *
	 * @return  void
	 */
	protected function registerHandlers()
	{
		$path = PathHelper::getSite('com_' . $this->getName()) . '/router/handler';

		foreach (Folder::files($path, false, Folder::PATH_BASENAME) as $file)
		{
			if (File::getExtension($file) !== 'php')
			{
				continue;
			}
			
			$name = File::stripExtension($file);

			include_once $path . '/' . $file;

			/** @var RouterHandlerInterface $handler */
			$class = $this->getHandlerClass($name);

			if (!class_exists($class))
			{
				continue;
			}

			$this->handlers[$name] = $handler = new $class($this);

			$this->registerView($handler->getViewconfiguration());
		}

		// After all view configurations registered, let's configure it.
		foreach ($this->handlers as $handler)
		{
			$handler->configure($handler->getViewconfiguration());
		}
	}

	/**
	 * getRuleClass
	 *
	 * @param string $view
	 *
	 * @return  string
	 */
	protected function getHandlerClass($view)
	{
		return ucfirst($this->getName()) . 'RouterHandler' . ucfirst($view);
	}

	/**
	 * getHandler
	 *
	 * @param string $name
	 *
	 * @return  RouterHandlerInterface
	 */
	public function getHandler($name)
	{
		if (isset($this->handlers[$name]))
		{
			return $this->handlers[$name];
		}

		return null;
	}

	/**
	 * getView
	 *
	 * @param string $name
	 *
	 * @return  RouterViewConfiguration
	 */
	public function getView($name)
	{
		$views = $this->getViews();

		if (isset($views[$name]))
		{
			return $views[$name];
		}

		return null;
	}

	/**
	 * __call
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return  array|mixed
	 */
	public function __call($name, $args)
	{
		$name = strtolower($name);

		if (strpos($name, 'get') === 0)
		{
			$name = substr($name, 3);
		}

		if (substr($name, -2) === 'id')
		{
			$name = substr($name, 0, -2);

			$handler = $this->getHandler($name);

			if ($handler)
			{
				return call_user_func_array(array($handler, 'getId'), $args);
			}

			return false;
		}
		elseif (substr($name, -7) === 'segment')
		{
			$name = substr($name, 0, -7);

			$handler = $this->getHandler($name);

			if ($handler)
			{
				return call_user_func_array(array($handler, 'getSegment'), $args);
			}

			return array();
		}

		return false;
	}
}
