<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Event;

use Windwalker\DI\Container;

/**
 * Class ListenerHelper
 *
 * @since 1.0
 */
class ListenerHelper
{
	/**
	 * Property linstener.
	 *
	 * @var  array
	 */
	protected static $listener = array();

	/**
	 * registerListeners
	 *
	 * @param string            $prefix
	 * @param \JEventDispatcher $dispatcher
	 * @param string            $path
	 *
	 * @return  void
	 */
	public static function registerListeners($prefix, $dispatcher, $path)
	{
		$dirs = new \DirectoryIterator($path);

		foreach ($dirs as $dir)
		{
			if ($dir->isFile() || $dir->isDot())
			{
				continue;
			}

			$class = sprintf('%s\\Listener\\%s\\%sListener', ucfirst($prefix), $dir, $dir);

			static::attach($class, $dispatcher);
		}
	}

	/**
	 * attach
	 *
	 * @param \JEvent|string|array $listener
	 * @param \JEventDispatcher    $dispatcher
	 *
	 * @return  mixed
	 */
	protected static function attach($listener, $dispatcher = null)
	{
		$dispatcher = $dispatcher ? : Container::getInstance()->get('event.dispatcher');

		if (is_string($listener) && class_exists($listener))
		{
			$listener = new $listener($dispatcher);
		}

		return $dispatcher->attach($listener);
	}
	/**
	 * detach
	 *
	 * @param \JEvent|string|array $listener
	 * @param \JEventDispatcher    $dispatcher
	 *
	 * @return  mixed
	 */
	protected static function detach($listener, $dispatcher = null)
	{
		$dispatcher = $dispatcher ? : Container::getInstance()->get('event.dispatcher');

		if (is_string($listener) && class_exists($listener))
		{
			if (empty(static::$listener[$listener]))
			{
				static::$listener[$listener] = $listener = new $listener($dispatcher);
			}
			else
			{
				$listener = static::$listener[$listener];
			}
		}

		return $dispatcher->detach($listener);
	}

	/**
	 * getListener
	 *
	 * @param string $prefix
	 * @param string $name
	 *
	 * @return  mixed
	 */
	protected static function getListener($prefix, $name)
	{
		$class = sprintf('%s\\Listener\\%s\\%sListener', ucfirst($prefix), ucfirst($name), ucfirst($name));

		return static::$listener[$class];
	}
}
