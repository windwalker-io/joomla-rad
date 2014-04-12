<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\System\Config;

/**
 * Interface ConfigInterface
 */
interface ConfigInterface
{
	/**
	 * getConfig
	 *
	 * @return  mixed
	 */
	public static function getConfig();

	/**
	 * getPath
	 *
	 * @return  string
	 */
	public static function getPath();
}
 