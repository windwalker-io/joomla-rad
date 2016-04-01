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
 * Class TableHelper
 */
abstract class TableHelper
{
	/**
	 * stripPrefix
	 *
	 * @param string  $table
	 * @param null    $prefix
	 *
	 * @return string
	 */
	static public function stripPrefix($table, $prefix = null)
	{
		$prefix = $prefix ?: \JFactory::getDbo()->getPrefix();

		$num = strlen($prefix);

		if (substr($table, 0, $num) == $prefix)
		{
			$table = '#__' . substr($table, $num);
		}

		return $table;
	}
}
