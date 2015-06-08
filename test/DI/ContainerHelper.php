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
	 * Property of original Application instance
	 *
	 * @var mixed
	 */
	protected static $originalApplication = null;

	/**
	 * Replace original Application instance
	 *
	 * @param mixed $app Application instance
	 *
	 * @return void
	 */
	public static function setApplication($app)
	{
		$container = Container::getInstance();

		if (empty($originApplication))
		{
			static::$originalApplication = $container->get('app');
		}

		$className = get_class($app);

		$container->alias('app', $className);
		$container->share($className, $app);
	}

	/**
	 * Restore original Application instance
	 *
	 * @return  void
	 */
	public static function restoreApplication()
	{
		$container = Container::getInstance();
		$className = get_class(static::$originalApplication);

		$container->alias('app', $className);
		$container->share($className, static::$originalApplication);
	}
}
