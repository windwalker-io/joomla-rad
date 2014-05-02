<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Html;

use Windwalker\String\String;

// No direct access
defined('_JEXEC') or die;

/**
 * HTML Builder helper.
 *
 * @since 2.0
 */
class HtmlBuilder
{
	/**
	 * Unpaired elements.
	 *
	 * @var  array
	 */
	protected static $unpairedElements = array(
		'img', 'br', 'hr', 'area', 'param', 'wbr', 'base', 'link', 'meta', 'input', 'option'
	);

	/**
	 * Create a html element.
	 *
	 * @param string $name    Element tag name.
	 * @param mixed  $content Element content.
	 * @param array  $attribs Element attributes.
	 *
	 * @return  string Created element string.
	 */
	public static function create($name, $content = '', $attribs = array())
	{
		$name = trim($name);

		$unpaired = in_array(strtolower($name), static::$unpairedElements);

		$tag = '<' . $name;

		foreach ((array) $attribs as $key => $value)
		{
			if ($value !== null && $value !== false && $value !== '')
			{
				$tag .= ' ' . $key . '=' . String::quote($value, '""');
			}
		}

		if ($content)
		{
			if (!($content instanceof HtmlElement))
			{
				$content = implode(PHP_EOL, (array) $content);
			}

			$tag .= '>' . PHP_EOL . "\t" . $content . PHP_EOL . '</' . $name . '>';
		}
		else
		{
			$tag .= $unpaired ? ' />' : '></' . $name . '>';
		}

		return $tag;
	}
}
