<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use Windwalker\String\Utf8String;

// No direct access
defined('_JEXEC') or die;

/**
 * Enhance static, and add some useful functions.
 *
 * @since 2.0
 */
class ArrayHelper extends \Windwalker\Utilities\ArrayHelper
{
	/**
	 * Function to convert array empty string to NULL values
	 *
	 * @param   array  $array    The source array to convert
	 *
	 * @return  array The converted array
	 *
	 * @since   2.1
	 */
	public static function toNull($array)
	{
		if (!is_array($array) && !is_object(is_object($array)))
		{
			return $array;
		}

		if (is_object($array))
		{
			$array = get_object_vars($array);
		}

		foreach ($array as &$value)
		{
			if (is_array($value) || is_object($value))
			{
				$value = static::toNull($value);
			}
			elseif ($value === '')
			{
				$value = null;
			}
		}

		return $array;
	}

	/**
	 * Transpose a two-dimensional matrix array.
	 *
	 * @param  array $array An array with two level.
	 *
	 * @return array An pivoted array.
	 */
	public static function transpose($array)
	{
		$array = (array) $array;
		$new   = array();
		$keys  = array_keys($array);

		foreach ($keys as $k => $val)
		{
			foreach ((array) $array[$val] as $k2 => $v2)
			{
				$new[$k2][$val] = $v2;
			}
		}

		return $new;
	}

	/**
	 * Same as ArrayHelper::pivot().
	 * From:
	 *          [0] => Array
	 *             (
	 *                 [value] => aaa
	 *                 [text] => aaa
	 *             )
	 *         [1] => Array
	 *             (
	 *                 [value] => bbb
	 *                 [text] => bbb
	 *             )
	 * To:
	 *         [value] => Array
	 *             (
	 *                 [0] => aaa
	 *                 [1] => bbb
	 *             )
	 *         [text] => Array
	 *             (
	 *                 [0] => aaa
	 *                 [1] => bbb
	 *             )
	 *
	 * @param   array $array An array with two level.
	 *
	 * @return  array An pivoted array.
	 */
	public static function pivotBySort($array)
	{
		$array = (array) $array;
		$new   = array();

		$array2 = $array;
		$first  = array_shift($array2);

		foreach ($array as $k => $v)
		{
			foreach ((array) $first as $k2 => $v2)
			{
				$new[$k2][$k] = $array[$k][$k2];
			}
		}

		return $new;
	}

	/**
	 * Pivot $origin['prefix_xxx'] to $target['prefix']['xxx'].
	 *
	 * @param   string $prefix A prefix text.
	 * @param   array  $origin Origin array to pivot.
	 * @param   array  $target A target array to store pivoted value.
	 *
	 * @return  array  Pivoted array.
	 */
	public static function pivotFromPrefix($prefix, $origin, $target = null)
	{
		$target = is_object($target) ? (object) $target : (array) $target;

		foreach ((array) $origin as $key => $row)
		{
			if (strpos($key, $prefix) === 0)
			{
				$key2 = Utf8String::substr($key, Utf8String::strlen($prefix));
				self::setValue($target, $key2, $row);
			}
		}

		return $target;
	}

	/**
	 * Pivot $origin['prefix']['xxx'] to $target['prefix_xxx'].
	 *
	 * @param   string $prefix A prefix text.
	 * @param   array  $origin Origin array to pivot.
	 * @param   array  $target A target array to store pivoted value.
	 *
	 * @return  array  Pivoted array.
	 */
	public static function pivotToPrefix($prefix, $origin, $target = null)
	{
		$target = is_object($target) ? (object) $target : (array) $target;

		foreach ((array) $origin as $key => $val)
		{
			$key = $prefix . $key;

			if (!self::getValue($target, $key))
			{
				self::setValue($target, $key, $val);
			}
		}

		return $target;
	}

	/**
	 * Pivot two-dimensional array to one-dimensional.
	 *
	 * @param   array &$array A two-dimension array.
	 *
	 * @return  array  Pivoted array.
	 */
	public static function pivotFromTwoDimension(&$array)
	{
		foreach ((array) $array as $val)
		{
			if (is_array($val) || is_object($val))
			{
				foreach ((array) $val as $key => $val2)
				{
					self::setValue($array, $key, $val2);
				}
			}
		}

		return $array;
	}

	/**
	 * Pivot one-dimensional array to two-dimensional array by a key list.
	 *
	 * @param   array|object &$array Array to pivot.
	 * @param   array        $keys   he fields' key list.
	 *
	 * @return  array  Pivoted array.
	 */
	public static function pivotToTwoDimension(&$array, $keys = array())
	{
		foreach ((array) $keys as $key)
		{
			if (is_object($array))
			{
				$array2 = clone $array;
			}
			else
			{
				$array2 = $array;
			}

			self::setValue($array, $key, $array2);
		}

		return $array;
	}

	/**
	 * filterRecursive
	 *
	 * @param  array     $array
	 * @param  callable  $callback
	 *
	 * @return  array
	 */
	public static function filterRecursive($array, $callback)
	{
		foreach ($array as &$value)
		{
			if (is_array($value))
			{
				$value = static::filterRecursive($value, $callback);
			}
		}

		return array_filter($array, $callback);
	}
}
