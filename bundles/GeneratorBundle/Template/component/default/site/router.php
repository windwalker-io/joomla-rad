<?php
/**
 * Part of {{extension.name.lower}} project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
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
 * 轉換網址
 *
 * ?view=items&category_id=23 => /category/23
 * ?views=item&category_id=39 => /category/39
 * ?view=item&title=abc&id=18 => /item/abc
 *
 * @param array &$query
 *
 * @return array
 */
function {{extension.name.cap}}BuildRoute(&$query)
{
	$segments = array();

	$router = CmsRouter::getInstance('{{extension.element.lower}}');

	$query = \Windwalker\Router\Route::build($query);

	if (!empty($query['view']))
	{
		$segments = $router->build($query['view'], $query);

		unset($query['view']);
	}

	return $segments;
}

/**
 * 轉換網址
 *
 * /category/23 => array("category_id" => 23,  "view"  => "items");
 * /category/39 => array("category_id" => 39,  "view"  => "items");
 * /item/abc    => array("id" => (int) abc_id, "title" => "abc", "view" => "item");
 *
 * @param array $segments
 *
 * @return array
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
