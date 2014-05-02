<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Helper;

use Windwalker\Debugger\Debugger;
use Windwalker\DI\Container;

/**
 * The Date Helper
 *
 * @since 2.0
 */
abstract class DateHelper
{
	/**
	 * Return the {@link JDate} object
	 *
	 * @param   mixed  $time      The initial time for the JDate object
	 * @param   mixed  $tzOffset  The timezone offset.
	 *
	 * @return  \JDate object
	 */
	public static function getDate($time = 'now', $tzOffset = null)
	{
		if (!$tzOffset)
		{
			$config = Container::getInstance()->get('joomla.config');

			$tzOffset = $config->get('offset');
		}

		return \JFactory::getDate($time, $tzOffset);
	}
}
