<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Router;

use Joomla\Uri\Uri;
use Windwalker\Joomla\Registry\DecoratingRegistry;
use Windwalker\Registry\Registry;
use Windwalker\Router\Matcher\MatcherInterface;
use Windwalker\Utilities\ArrayHelper;

/**
 * The RadRouter class.
 *
 * @property-read  Route     route
 * @property-read  Registry  extra
 *
 * @since  2.1
 */
class RadRouter extends Router
{
	/**
	 * Singleton instance.
	 *
	 * @var  CmsRouter
	 */
	static protected $instance = array();

	/**
	 * Property queries.
	 *
	 * @var  array
	 */
	protected $queries;

	/**
	 * Property menu.
	 *
	 * @var  \JMenu
	 */
	protected $menu;

	/**
	 * Property matched.
	 *
	 * @var  Route
	 */
	protected $matched;

	/**
	 * Property extra.
	 *
	 * @var  Registry
	 */
	protected $extra;

	/**
	 * Singleton.
	 *
	 * @param string $option The component option name.
	 * @param \JMenu $menu
	 *
	 * @return static Router instance.
	 */
	public static function getInstance($option, $menu = null)
	{
		if (empty(self::$instance[$option]))
		{
			self::$instance[$option] = new static($menu);
		}

		return self::$instance[$option];
	}

	/**
	 * Class init.
	 *
	 * @param \JMenu           $menu
	 * @param array            $routes
	 * @param MatcherInterface $matcher
	 */
	public function __construct($menu, array $routes = array(), MatcherInterface $matcher = null)
	{
		$this->menu = $menu;
		$this->extra = new Registry;

		parent::__construct($routes, $matcher);
	}

	/**
	 * Find and execute the appropriate view name based on a given route.
	 *
	 * @param   string  $route  The route string for which to find a view.
	 *
	 * @return  mixed   The return value of the view name.
	 */
	public function getView($route)
	{
		// Get the view name based on the route patterns and requested route.
		return $name = $this->match($route);
	}

	/**
	 * Build route by resource setting.
	 *
	 * @param string $name       The route name.
	 * @param array  $queries    The queries.
	 * @param bool   $rootSlash  Add root slash or not.
	 *
	 * @return  array|string  Segments, can be string or array.
	 */
	public function build($name, $queries = array(), $rootSlash = false)
	{
		$segments = parent::build($name, $queries, $rootSlash);

		$uri = new Uri($segments);
		$segments = $uri->getPath();

		$this->queries = $uri->getQuery(true);

		return explode('/', $segments);
	}

	/**
	 * Generate route, the queries must be reference.
	 *
	 * @param string $name       The route name.
	 * @param array  $queries    The queries.
	 * @param bool   $rootSlash  Add root slash or not.
	 *
	 * @return  array|string  Segments, can be string or array.
	 */
	public function generate($name, &$queries = array(), $rootSlash = false)
	{
		if (!array_key_exists($name, $this->routes))
		{
			throw new \OutOfRangeException('Route: ' . $name . ' not found.');
		}

		$route = $this->routes[$name];

		$extra = $route->getExtra();

		$segments = null;
		$replace = false;

		unset($queries['_resource']);

		if (isset($extra['buildHandler']) && is_callable($extra['buildHandler']))
		{
			$segments = call_user_func_array($extra['buildHandler'], array(&$queries, &$replace, $this->menu));

			$this->queries = $queries;
		}

		if (!$replace)
		{
			$segments = $this->build($name, $queries, $rootSlash);
		}

		$queries = $this->getQueries();

		return $segments;
	}

	/**
	 * Match route.
	 *
	 * @param string $route
	 * @param string $method
	 * @param array  $options
	 *
	 * @return  Route|boolean
	 */
	public function match($route, $method = 'GET', $options = array())
	{
		$matched = parent::match($route, $method, $options);

		$extra = $matched->getExtra();

		if (isset($extra['parseHandler']) && is_callable($extra['parseHandler']))
		{
			$variables = call_user_func_array($extra['parseHandler'], array($matched->getVariables()));

			$matched->setVariables($variables);
		}

		$this->matched = $matched;
		$this->extra->reset()->load($matched->getExtra());

		return $matched;
	}

	/**
	 * Build route by raw url.
	 *
	 * @param array &$queries
	 *
	 * @return  array
	 */
	public function buildByRaw(&$queries)
	{
		if (empty($queries['view']))
		{
			return array();
		}

		foreach ($this->routes as $view => $map)
		{
			$vars = $map->getVariables();

			if (ArrayHelper::getValue($vars, 'controller') == $queries['view'])
			{
				unset($queries['view']);

				return $this->generate($view, $map);

				break;
			}
		}

		return array();
	}

	/**
	 * Method to get property Queries
	 *
	 * @return  array
	 */
	public function getQueries()
	{
		return $this->queries;
	}

	/**
	 * Method to set property queries
	 *
	 * @param   array $queries
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setQueries($queries)
	{
		$this->queries = $queries;

		return $this;
	}

	/**
	 * Method to get property Matched
	 *
	 * @return  Route
	 */
	public function getMatched()
	{
		return $this->matched;
	}

	/**
	 * Method to set property matched
	 *
	 * @param   Route $matched
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setMatched($matched)
	{
		$this->matched = $matched;

		return $this;
	}

	/**
	 * Method to get property Extra
	 *
	 * @return  Registry
	 */
	public function getExtra()
	{
		return $this->extra;
	}

	/**
	 * Method to set property extra
	 *
	 * @param   Registry $extra
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setExtra($extra)
	{
		$extra = DecoratingRegistry::toWindwalkerRegistry($extra);

		$this->extra = $extra;

		return $this;
	}

	/**
	 * __get
	 *
	 * @param   string  $name
	 *
	 * @return  mixed
	 */
	public function __get($name)
	{
		$allow = array('extra', 'matched');

		if (in_array($name, $allow))
		{
			return $this->$name;
		}

		throw new \OutOfRangeException('Property: ' . $name . ' not exists.');
	}
}
