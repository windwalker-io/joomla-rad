<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Router;

use JInput as Input;

/**
 * Router class.
 *
 * Based on Joomla Router.
 *
 * @since 2.0
 */
class Router
{
	/**
	 * Controller class name prefix for creating controller objects by name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $controllerPrefix;

	/**
	 * The default page controller name for an empty route.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $default;

	/**
	 * An input object from which to derive the route.
	 *
	 * @var    Input
	 * @since  1.0
	 */
	protected $input;

	/**
	 * An array of rules, each rule being an associative array('regex'=> $regex, 'vars' => $vars, 'controller' => $controller)
	 * for routing the request.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $maps = array();

	/**
	 * Property resources.
	 *
	 * @var  array
	 */
	protected $resources = array();

	/**
	 * Property parseHandler.
	 *
	 * @var  array
	 */
	protected $parseHandler = array();

	/**
	 * Property buildHandler.
	 *
	 * @var  array
	 */
	protected $buildHandler = array();

	/**
	 * Constructor.
	 *
	 * @param   Input  $input  An optional input object from which to derive the route.  If none
	 *                         is given than the input from the application object will be used.
	 *
	 * @since   1.0
	 */
	public function __construct(Input $input = null)
	{
		$this->input = ($input === null) ? new Input : $input;
	}

	/**
	 * Register a route map.
	 *
	 * @param   string    $name           Route resource name.
	 * @param   string    $pattern        Route pattern.
	 * @param   string    $controller     Matched controller name.
	 * @param   callable  $buildHandler   The build callback.
	 * @param   callable  $parserHandler  The parse callback.
	 *
	 * @return  Router Return self to support chaining.
	 */
	public function register($name, $pattern, $controller, $buildHandler = null, $parserHandler = null)
	{
		return $this->addMap($name, $pattern, $controller)
			->addResource($name, $pattern)
			->setBuildHandler($name, $buildHandler)
			->setParseHandler($name, $parserHandler);
	}

	/**
	 * Add a route map to the router. If the pattern already exists it will be overwritten.
	 *
	 * @param   string $name       The key name.
	 * @param   string $pattern    The route pattern to use for matching.
	 * @param   string $controller The controller name to map to the given pattern.
	 *
	 * @return  Router  Returns itself to support chaining.
	 *
	 * @since   1.0
	 */
	public function addMap($name, $pattern, $controller)
	{
		// Sanitize and explode the pattern.
		$pattern = explode('/', trim(parse_url((string) $pattern, PHP_URL_PATH), ' /'));

		// Prepare the route variables
		$vars = array();

		// Initialize regular expression
		$regex = array();

		// Loop on each segment
		foreach ($pattern as $segment)
		{
			if ($segment == '*')
			{
				// Match a splat with no variable.
				$regex[] = '.*';
			}
			elseif ($segment[0] == '*')
			{
				// Match a splat and capture the data to a named variable.
				$vars[] = substr($segment, 1);
				$regex[] = '(.*)';
			}
			elseif ($segment[0] == '\\' && $segment[1] == '*')
			{
				// Match an escaped splat segment.
				$regex[] = '\*' . preg_quote(substr($segment, 2));
			}
			elseif ($segment == ':')
			{
				// Match an unnamed variable without capture.
				$regex[] = '[^/]*';
			}
			elseif ($segment[0] == ':')
			{
				// Match a named variable and capture the data.
				$vars[] = substr($segment, 1);
				$regex[] = '([^/]*)';
			}
			elseif ($segment[0] == '\\' && $segment[1] == ':')
			{
				// Match a segment with an escaped variable character prefix.
				$regex[] = preg_quote(substr($segment, 1));
			}
			else
			{
				// Match the standard segment.
				$regex[] = preg_quote($segment);
			}
		}

		$this->maps[$name] = array(
			'regex' => chr(1) . '^' . implode('/', $regex) . '$' . chr(1),
			'vars'  => $vars,
			'controller' => (string) $controller
		);

		return $this;
	}

	/**
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 */
	public function getController($route)
	{
		// Get the controller name based on the route patterns and requested route.
		return $this->parseRoute($route);
	}

	/**
	 * Set the controller name prefix.
	 *
	 * @param   string  $prefix  Controller class name prefix for creating controller objects by name.
	 *
	 * @return  Router  Returns itself to support chaining.
	 *
	 * @since   1.0
	 */
	public function setControllerPrefix($prefix)
	{
		$this->controllerPrefix	= (string) $prefix;

		return $this;
	}

	/**
	 * Set the default controller name.
	 *
	 * @param   string  $name  The default page controller name for an empty route.
	 *
	 * @return  Router  Returns itself to support chaining.
	 *
	 * @since   1.0
	 */
	public function setDefaultController($name)
	{
		$this->default = (string) $name;

		return $this;
	}

	/**
	 * Parse the given route and return the name of a controller mapped to the given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  string  The controller name for the given route excluding prefix.
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	protected function parseRoute($route)
	{
		$controller = false;

		// Trim the query string off.
		$route = preg_replace('/([^?]*).*/u', '\1', $route);

		// Sanitize and explode the route.
		$route = trim(static::parseUrl($route, PHP_URL_PATH), ' /');

		// If the route is empty then simply return the default route.  No parsing necessary.
		if ($route == '')
		{
			return $this->default;
		}

		// Iterate through all of the known route maps looking for a match.
		foreach ($this->maps as $key => $rule)
		{
			if (preg_match($rule['regex'], $route, $matches))
			{
				// If we have gotten this far then we have a positive match.
				$controller = $rule['controller'];

				// Time to set the input variables.
				// We are only going to set them if they don't already exist to avoid overwriting things.
				foreach ($rule['vars'] as $i => $var)
				{
					$this->input->def($var, $matches[$i + 1]);

					// Don't forget to do an explicit set on the GET superglobal.
					$this->input->get->def($var, $matches[$i + 1]);
				}

				$this->input->def('_rawRoute', $route);

				$name = $key;

				break;
			}
		}

		// We were unable to find a route match for the request.  Panic.
		if (!$controller)
		{
			throw new \InvalidArgumentException(sprintf('Unable to handle request for route `%s`.', $route), 404);
		}

		if (is_callable($this->parseHandler[$name]))
		{
			call_user_func_array($this->parseHandler[$name], array($controller, $this->input));
		}

		return $controller;
	}

	/**
	 * Add resource for build route.
	 *
	 * @param string $name
	 * @param string $pattern
	 *
	 * @return  Router
	 */
	public function addResource($name, $pattern)
	{
		$this->resources[$name] = $pattern;

		return $this;
	}

	/**
	 * Build route.
	 *
	 * @param string $name     Route resource name.
	 * @param array  &$queries Http queries.
	 *
	 * @return  array
	 */
	public function build($name, &$queries)
	{
		if (empty($this->resources[$name]))
		{
			return array();
		}

		if (is_callable($this->buildHandler[$name]))
		{
			call_user_func_array($this->buildHandler[$name], array($queries));
		}

		$replace = array();

		$pattern = $this->resources[$name];

		foreach ($this->maps[$name]['vars'] as $key)
		{
			$var = isset($queries[$key]) ? $queries[$key] : $this->input->get($key, 'null');

			if (is_array($var) || is_object($var))
			{
				$var = implode('/', (array) $var);

				$key2 = '*' . $key;

				$replace[$key2] = $var;
			}
			else
			{
				$key2 = ':' . $key;

				$replace[$key2] = $var;
			}

			if (strpos($pattern, $key2) !== false)
			{
				unset($queries[$key]);
			}
		}

		$pattern = strtr($pattern, $replace);

		return explode('/', $pattern);
	}

	/**
	 * Does a UTF-8 safe version of PHP parse_url function
	 *
	 * @param   string  $url  URL to parse
	 *
	 * @return  mixed  Associative array or false if badly formed URL.
	 */
	public static function parseUrl($url)
	{
		$result = false;

		// Build arrays of values we need to decode before parsing
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "$", ",", "/", "?", "#", "[", "]");

		// Create encoded URL with special URL characters decoded so it can be parsed
		// All other characters will be encoded
		$encodedURL = str_replace($entities, $replacements, urlencode($url));

		// Parse the encoded URL
		$encodedParts = parse_url($encodedURL);

		// Now, decode each value of the resulting array
		if ($encodedParts)
		{
			foreach ($encodedParts as $key => $value)
			{
				$result[$key] = urldecode(str_replace($replacements, $entities, $value));
			}
		}

		return implode('', $result);
	}

	/**
	 * setParseHandler
	 *
	 * @param string   $name
	 * @param callable $handler
	 *
	 * @return  Router  Return self to support chaining.
	 */
	public function setParseHandler($name, $handler)
	{
		$this->parseHandler[$name] = $handler;

		return $this;
	}

	/**
	 * setBuildHandler
	 *
	 * @param string   $name
	 * @param callable $handler
	 *
	 * @return  Router  Return self to support chaining.
	 */
	public function setBuildHandler($name, $handler)
	{
		$this->buildHandler[$name] = $handler;

		return $this;
	}
}
