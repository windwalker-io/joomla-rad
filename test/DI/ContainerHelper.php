<?php
/**
 * Part of windwalker-joomla-rad-test project.
 *
 * @copyright  Copyright (C) 2011 - 2015 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\DI;

use Windwalker\DI\Container;

/**
 * Class ContainerHelper
 */
class ContainerHelper
{
	/**
	 * setApplication
	 *
	 * @param mixed $app Application instance
	 *
	 * @return void
	 */
	public static function setApplication($app)
	{
		$container = Container::getInstance();

		$container->alias('app', 'Application');
		$container->share('Application', function() use ($app)
		{
			return $app;
		});
	}
}
