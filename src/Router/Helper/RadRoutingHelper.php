<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Router\Helper;

use Windwalker\Helper\PathHelper;
use Windwalker\Registry\Registry;
use Windwalker\Router\Route;
use Windwalker\Router\Router;
use Windwalker\Router\RouterInterface;

/**
 * The RadRoutingHelper class.
 *
 * @since  2.1
 */
class RadRoutingHelper
{
	const TYPE_JSON = 'json';
	const TYPE_YAML = 'yaml';

	/**
	 * Routing rules storage.
	 *
	 * @var  mixed
	 */
	protected static $routing = null;

	/**
	 * The registered flag.
	 *
	 * @var  boolean
	 */
	private static $registered = false;

	/**
	 * Get routing config.
	 *
	 * @param string $option The component option name.
	 * @param string $type   The routing file type.
	 *
	 * @return  mixed
	 */
	public static function getRouting($option, $type = self::TYPE_YAML)
	{
		if (self::$routing)
		{
			return self::$routing;
		}

		$path = PathHelper::getSite($option);

		$fileType = $type == static::TYPE_YAML ? 'yml' : $type;

		$data = new Registry;
		$data->loadFile($path . '/routing.' . $fileType, $type);

		return self::$routing = $data->toArray();
	}

	/**
	 * Register routing.
	 *
	 * @param Router  $router Router object.
	 * @param string  $option The component option name.
	 * @param string  $type   The routing file type.
	 *
	 * @return RouterInterface
	 */
	public static function registerRouting(Router $router, $option, $type = self::TYPE_JSON)
	{
		// Don't register twice.
		if (self::$registered)
		{
			return $router;
		}

		// Register routers.
		$maps = static::getRouting($option, $type);

		$default = array(
			'pattern'  => null,
			'view'     => null,
			'task'     => null,
			'layout'   => null,
			'format'   => null,
			'methods'  => null,
			'requirements' => null,
			'extra'   => null,
			'buildHandler' => null,
			'parseHandler' => null,
		);

		foreach ((array) $maps as $name => $map)
		{
			$map = array_merge($default, $map);

			if (empty($map['pattern']))
			{
				throw new \LogicException('Are you kidding me? Add pattern to: ' . $name);
			}

			$variables = array(
				'view'   => $map['view'],
				'task'   => $map['task'],
				'layout' => $map['layout'],
				'format' => $map['format']
			);

			$allowMethods = (array) $map['methods'];

			$options['requirements'] = (array) $map['requirements'];
			$options['extra']        = (array) $map['extra'];

			$options['extra']['buildHandler'] = !empty($map['buildHandler']) ? $map['buildHandler'] : '';
			$options['extra']['parseHandler'] = !empty($map['parseHandler']) ? $map['parseHandler'] : '';

			$router->addRoute(new Route($name, $map['pattern'], $variables, $allowMethods, $options));
		}

		return $router;
	}

	/**
	 * Execute if type handler exists.
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

	/**
	 * buildFromView
	 *
	 * @param string     $component
	 * @param string     $view
	 * @param array      $queries
	 * @param boolean    $replace
	 * @param \JMenuSite $menu
	 *
	 */
	protected static function buildFromView($component, $view, &$queries, &$replace, \JMenuSite $menu)
	{
		// Get all com_flower menus
		$menuItems = $menu->getItems('component', $component);

		// Find matched menu item.
		foreach ($menuItems as $menuItem)
		{
			if (isset($menuItem->query['view']) && $menuItem->query['view'] == $view)
			{
				// Replace core route rule.
				$replace = true;

				// Only return menu Itemid then Joomla will convert to menu alias
				$queries = array('Itemid' => $menuItem->id);
			}
		}

		// No menu matched, follows default rule.
	}

	/**
	 * buildFromViewAndId
	 *
	 * @param string  $component
	 * @param string  $view
	 * @param array   $queries
	 * @param boolean $replace
	 * @param \JMenu  $menu
	 */
	public static function buildFromViewAndId($component, $view, &$queries, &$replace, \JMenu $menu)
	{
		// Get all com_flower menus
		$menuItems = $menu->getItems('component', $component);

		// Find matched menu item.
		foreach ($menuItems as $menuItem)
		{
			if (isset($menuItem->query['view']) && $menuItem->query['view'] == $view &&
				isset($menuItem->query['id']) && $menuItem->query['id'] == $queries['id'])
			{
				// Replace core route rule.
				$replace = true;

				// Only return menu Itemid then Joomla will convert to menu alias
				$queries = array('Itemid' => $menuItem->id);
			}
		}

		// No menu matched, follows default rule.
	}
}
