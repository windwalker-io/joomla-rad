<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace {{extension.name.cap}}\Router;

/**
 * The SiteRoute class.
 *
 * @since  1.0
 */
abstract class SiteRoute extends \Windwalker\Router\SiteRoute
{
	/**
	 * Property defaultOption.
	 *
	 * @var  string
	 */
	protected static $defaultOption = '{{extension.element.lower}}';
}
