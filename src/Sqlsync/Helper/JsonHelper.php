<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Helper;

/**
 * Class JsonHelper
 */
abstract class JsonHelper
{
	/**
	 * encode
	 *
	 * @param array  $data
	 * @param null   $option
	 *
	 * @return mixed|string
	 */
	public static function encode($data, $option = null)
	{
		if (version_compare(PHP_VERSION, '5.4', '>'))
		{
			$option = $option | JSON_PRETTY_PRINT;
		}

		return json_encode($data, $option);
	}
}