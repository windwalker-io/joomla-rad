<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Router\Helper;

use Windwalker\Helper\PathHelper;
use Windwalker\Router\Router;

/**
 * Class RoutingHelper
 *
 * @since 1.0
 */
class RoutingHelper
{
	/**
	 * Property routing.
	 *
	 * @var  mixed
	 */
	protected static $routing = null;

	/**
	 * Property registered.
	 *
	 * @var  boolean
	 */
	private static $registered = false;

	/**
	 * Get routing config.
	 *
	 * @param string $option
	 *
	 * @return  mixed
	 */
	public static function getRouting($option)
	{
		if (self::$routing)
		{
			return self::$routing;
		}

		$path = PathHelper::getSite($option);

		return self::$routing = json_decode(file_get_contents($path . '/routing.json'));
	}

	/**
	 * registerRouting
	 *
	 * @param Router $router
	 * @param string $option
	 *
	 * @throws \LogicException
	 * @return  Router
	 */
	public static function registerRouting(Router $router, $option)
	{
		// Don't register twice.
		if (self::$registered)
		{
			return $router;
		}

		// Register routers.
		$maps = static::getRouting($option);

		foreach ((array) $maps as $name => $map)
		{
			if (empty($map->pattern) || empty($map->view))
			{
				// throw new \LogicException('Are you kidding me? no map, no run! Add pattern and view to: ' . $name);
			}

			$buildHandler = !empty($map->buildHandler) ? $map->buildHandler : '';
			$parseHandler = !empty($map->parseHandler) ? $map->parseHandler : '';

			$router->register($name, $map->pattern, $map->view, $buildHandler, $parseHandler);
		}

		return $router;
	}

	/**
	 * Execute if type method exists.
	 *
	 * @param string  $type  Type name.
	 * @param string  $view  View name.
	 * @param \JInput $input Input object.
	 *
	 * @return  void
	 */
	public static function handleRoute($type, $view, $input)
	{
		$method = 'handle' . ucfirst($type);

		if (is_callable(array(__CLASS__, $method)))
		{
			call_user_func_array(array(__CLASS__, $method), array($view, $input));
		}
	}
}
 