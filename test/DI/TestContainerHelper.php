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
class TestContainerHelper
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

		\JFactory::$application = $app;
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

		\JFactory::$application = static::$originalApplication;
	}

	/**
	 * deleteChildInstance
	 *
	 * @param   string  $name  Container name
	 *
	 * @return  void
	 */
	public static function deleteChildInstance($name)
	{
		$name = trim((string) $name);

		if (empty($name))
		{
			return;
		}

		$ref = new \ReflectionProperty('Windwalker\DI\Container', 'children');

		$ref->setAccessible(true);

		$value = $ref->getValue();

		$value[$name] = null;

		$ref->setValue($value);
		$ref->setAccessible(false);
	}
}
