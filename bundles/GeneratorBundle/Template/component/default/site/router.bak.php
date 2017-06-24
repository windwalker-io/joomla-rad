<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\Router\Helper\RadRoutingHelper;
use Windwalker\Router\RadRouter;

include_once JPATH_ADMINISTRATOR . '/components/{{extension.element.lower}}/src/init.php';

if (!class_exists('Windwalker\Windwalker'))
{
	return;
}

/**
 * Routing class from com_content
 *
 * @since  1.0
 */
class {{extension.name.cap}}Router extends JComponentRouterBase
{
	/**
	 * Property router.
	 *
	 * @var  RadRouter
	 */
	protected $router;

	/**
	 * Class constructor.
	 *
	 * @param   JApplicationCms $app  Application-object that the router should use
	 * @param   JMenu           $menu Menu-object that the router should use
	 *
	 * @since   3.4
	 */
	public function __construct($app = null, $menu = null)
	{
		parent::__construct($app, $menu);

		// Prepare Router
		$this->router = RadRouter::getInstance('{{extension.element.lower}}', $this->menu);

		// Register routing config and inject Router object into it.
		$this->router = RadRoutingHelper::registerRouting($this->router, '{{extension.element.lower}}', RadRoutingHelper::TYPE_YAML);
	}

	/**
	 * Generic method to preprocess a URL
	 *
	 * @param   array $query An associative array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   1.0
	 */
	public function preprocess($query)
	{
		return $query;
	}

	/**
	 * Build method for URLs
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array &$query An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   1.0
	 */
	public function build(&$query)
	{
		// Find menu matches, and return matched Itemid.
		$query = \Windwalker\Router\RadRoute::build($query);

		// Your custom build rules here
		// ------------------------------------------------

		// ------------------------------------------------
		// End custom rules

		// If _resource exists, we use resource key to build route.
		if (!empty($query['_resource']))
		{
			$segments = $this->router->generate($query['_resource'], $query);

			unset($query['view'], $query['_rawRoute']);
		}
		else
		{
			$segments = $this->router->buildByRaw($query);
		}

		if (!isset($query['option']))
		{
			$query['option'] = '{{extension.element.lower}}';
		}

		return (array) $segments;
	}

	/**
	 * Parse method for URLs
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array &$segments The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   1.0
	 */
	public function parse(&$segments)
	{
		// Your custom parse rules here
		// ------------------------------------------------

		// ------------------------------------------------
		// End custom rules

		$segs = implode('/', $segments);

		// OK, let's fetch view name.
		$matched = $this->router->match(str_replace(':', '-', $segs));

		return $matched->getVariables();
	}
}
