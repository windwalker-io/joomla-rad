<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use Windwalker\Data\Data;
use Windwalker\String\StringHelper;
use Windwalker\String\Utf8String;
use Windwalker\Utilities\Reflection\ReflectionHelper;

/**
 * The BacktraceHelper class.
 *
 * @since  2.1.6
 */
class BacktraceHelper
{
	/**
	 * whoCallMe
	 *
	 * @param int $backwards
	 *
	 * @return  array
	 */
	public static function whoCallMe($backwards = 2)
	{
		return static::normalizeBacktrace(debug_backtrace()[$backwards]);
	}

	/**
	 * normalizeBacktrace
	 *
	 * @param   array  $trace
	 *
	 * @return  array
	 */
	public static function normalizeBacktrace(array $trace)
	{
		$trace = new Data($trace);

		$args = [];

		foreach ($trace['args'] as $arg)
		{
			if (is_array($arg))
			{
				$arg = 'Array';
			}
			elseif (is_object($arg))
			{
				$arg = ReflectionHelper::getShortName($arg);
			}
			elseif (is_string($arg))
			{
				if (Utf8String::strlen($arg) > 20)
				{
					$arg = Utf8String::substr($arg, 0, 20) . '...';
				}

				$arg = StringHelper::quote($arg);
			}
			elseif (is_null($arg))
			{
				$arg = 'NULL';
			}
			elseif (is_bool($arg))
			{
				$arg = $arg ? 'TRUE' : 'FALSE';
			}

			$args[] = $arg;
		}

		return array(
			'file' => $trace['file'] ? $trace['file'] . ' (' . $trace['line'] . ')' : null,
			'function' => ($trace['class'] ? $trace['class'] . $trace['type'] : null) . $trace['function'] .
				sprintf('(%s)', implode(', ', $args))
		);
	}

	/**
	 * normalizeBacktraces
	 *
	 * @param   array  $traces
	 *
	 * @return  array
	 */
	public static function normalizeBacktraces(array $traces)
	{
		$return = [];

		foreach ($traces as $trace)
		{
			$return[] = $trace ? static::normalizeBacktrace($trace) : null;
		}

		return $return;
	}
}
