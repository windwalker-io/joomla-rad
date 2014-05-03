<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\String;

use JString;
use Windwalker\Helper\ArrayHelper;

/**
 * Windwalker String. Based on Joomla String.
 *
 * @since 2.0
 */
class String extends JString
{
	/**
	 * Quote a string.
	 *
	 * @param   string $string The string to quote.
	 * @param   string $quote  The quote symbol.
	 *
	 * @return  string Quoted string.
	 */
	public static function quote($string, $quote = "''")
	{
		if (empty($quote[1]))
		{
			$quote[1] = $quote[0];
		}

		return $quote[0] . $string . $quote[1];
	}

	/**
	 * Back quote a string.
	 *
	 * @param   string $string The string to quote.
	 *
	 * @return  string Quoted string.
	 */
	public static function backquote($string)
	{
		return static::quote($string, '``');
	}

	/**
	 * Parse variable and replace it. This method is a simple template engine.
	 *
	 * Example: The {{ foo.bar.yoo }} will be replace to value of `$data['foo']['bar']['yoo']`
	 *
	 * @param   string $string The template to replace.
	 * @param   array  $data   The data to find.
	 * @param   array  $tags   The variable tags.
	 *
	 * @return  string Replaced template.
	 */
	public static function parseVariable($string, $data = array(), $tags = array('{{', '}}'))
	{
		return preg_replace_callback(
			'/\{\{\s*(.+?)\s*\}\}/',
			function($match) use ($data)
			{
				$return = ArrayHelper::getByPath($data, $match[1]);

				if (is_array($return) || is_object($return))
				{
					return print_r($return, 1);
				}
				else
				{
					return $return;
				}
			},
			$string
		);
	}
}
