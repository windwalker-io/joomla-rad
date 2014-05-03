<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\String;

use Joomla\String\Normalise;

/**
 * The normalise class based on Joomla Normalise.
 *
 * @since 2.0
 */
class StringNormalise extends Normalise
{
	/**
	 * Convert to standard PSR-0 class name.
	 *
	 * @param   string $class The class name string.
	 *
	 * @return  string Normalised class name.
	 */
	public static function toClassNamespace($class)
	{
		$class = trim($class, '\\');

		$class = str_replace('\\', ' ', $class);

		$class = ucwords($class);

		return str_replace(' ', '\\', $class);
	}
}
