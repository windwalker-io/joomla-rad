<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync;

use Joomla\Registry\Registry;
use Windwalker\Sqlsync\Registry\Format\Json;

/**
 * Class Config
 */
class Config extends Registry
{
	/**
	 * Get a namespace in a given string format
	 *
	 * @param   string  $format   Format to return the string in
	 * @param   mixed   $options  Parameters used by the formatter, see formatters for more info
	 *
	 * @return  string   Namespace in string format
	 *
	 * @since   1.0
	 */
	public function toString($format = 'JSON', $options = array())
	{
		if (strtolower($format) == 'json')
		{
			$handler = new Json;
		}
		else
		{
			$handler = AbstractRegistryFormat::getInstance($format);
		}

		return $handler->objectToString($this->data, $options);
	}
}
