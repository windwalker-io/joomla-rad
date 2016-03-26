<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

namespace {{extension.name.cap}}\Router;

use Windwalker\Router\RadRoute;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} route.
 *
 * @since 1.0
 */
class Route extends RadRoute
{
	/**
	 * Property defaultOption.
	 *
	 * @var  string
	 */
	protected static $defaultOption = '{{extension.element.lower}}';
}
