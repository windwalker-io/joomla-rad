<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\Router\CmsRouter;
use Windwalker\Router\Helper\RoutingHelper;

include_once JPATH_ADMINISTRATOR . '/components/{{extension.element.lower}}/src/init.php';

// Prepare Router
$router = CmsRouter::getInstance('{{extension.element.lower}}');

// Register routing config and inject Router object into it.
$router = RoutingHelper::registerRouting($router, '{{extension.element.lower}}');

/**
 * {{extension.name.cap}}BuildRoute
 *
 * @param array &$query
 *
 * @return  array
 */
function {{extension.name.cap}}BuildRoute(&$query)
{
	$segments = array();

	$router = CmsRouter::getInstance('{{extension.element.lower}}');

	$query = \Windwalker\Router\Route::build($query);

	if (!empty($query['_resource']))
	{
		$segments = $router->build($query['_resource'], $query);

		unset($query['_resource']);
	}
	else
	{
		$segments = $router->buildByRaw($query);
	}

	return $segments;
}

/**
 * {{extension.name.cap}}ParseRoute
 *
 * @param array $segments
 *
 * @return  array
 */
function {{extension.name.cap}}ParseRoute($segments)
{
	$router = CmsRouter::getInstance('{{extension.element.lower}}');

	$segments = implode('/', $segments);

	// OK, let's fetch view name.
	$view = $router->getView(str_replace(':', '-', $segments));

	if ($view)
	{
		return array('view' => $view);
	}

	return array();
}
