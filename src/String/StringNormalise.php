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
 * Class StringNormalise
 *
 * @since 1.0
 */
class StringNormalise extends Normalise
{
	/**
	 * toClassNamespace
	 *
	 * @param string $class
	 *
	 * @return  mixed
	 */
	public static function toClassNamespace($class)
	{
		$class = trim($class, '\\');

		$class = str_replace('\\', ' ', $class);

		$class = ucwords($class);

		return str_replace(' ', '\\', $class);
	}
}
