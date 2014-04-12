<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Helper;

use Joomla\String\Normalise;
use Windwalker\String\StringNormalise;

/**
 * Class ReflectionHelper
 *
 * @since 1.0
 */
class ReflectionHelper
{
	/**
	 * Property refs.
	 *
	 * @var  array
	 */
	protected static $refs = array();

	/**
	 * get
	 *
	 * @param string|object $class
	 *
	 * @return  mixed
	 */
	public static function get($class)
	{
		return static::getReflection($class);
	}

	/**
	 * getReflection
	 *
	 * @param string|object $class
	 *
	 * @return  mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	protected static function getReflection($class)
	{
		if (is_object($class))
		{
			$class = get_class($class);
		}

		if (!is_string($class))
		{
			throw new \InvalidArgumentException('ReflectionClass need string name or object.');
		}

		$class = StringNormalise::toClassNamespace($class);

		if (empty(static::$refs[$class]))
		{
			static::$refs[$class] = new \ReflectionClass($class);
		}

		return static::$refs[$class];
	}

	/**
	 * getPath
	 *
	 * @param string|object $class
	 *
	 * @return  string
	 */
	public static function getPath($class)
	{
		$ref = static::getReflection($class);

		return $ref->getFileName();
	}

	/**
	 * __callStatic
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return  mixed
	 */
	public static function __callStatic($name, $args)
	{
		$class = array_shift($args);

		$ref = static::getReflection($class);

		return call_user_func_array(array($ref, $name), $args);
	}
}
