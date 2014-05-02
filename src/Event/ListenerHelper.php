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
 * The helper to handle event listeners registration.
 *
 * @since 2.0
 */
class ListenerHelper
{
	/**
	 * listeners storage.
	 *
	 * @var  array
	 */
	protected static $listener = array();

	/**
	 * Auto register listeners.
	 *
	 * @param string            $prefix     Component prefix name.
	 * @param \JEventDispatcher $dispatcher The event dispatcher object.
	 * @param string            $path       The path of listeners' folders.
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
	 * Attach a listener.
	 *
	 * @param \JEvent|string|array $listener   The listener to attach to dispatcher.
	 * @param \JEventDispatcher    $dispatcher The EventDispatcher.
	 *
	 * @return  mixed  True if the observer object was attached.
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
	 * Detach a listener.
	 *
	 * @param \JEvent|string|array $listener   The listener name to detach.
	 * @param \JEventDispatcher    $dispatcher The EventDispatcher.
	 *
	 * @return  boolean True if the observer object was detached.
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
	 * Get listener by name.
	 *
	 * @param string $prefix Component prefix name.
	 * @param string $name   Listener name.
	 *
	 * @return  \JPlugin Found listener.
	 */
	protected static function getListener($prefix, $name)
	{
		$class = sprintf('%s\\Listener\\%s\\%sListener', ucfirst($prefix), ucfirst($name), ucfirst($name));

		return static::$listener[$class];
	}
}
