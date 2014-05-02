<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Helper;

use Joomla\String\Inflector;
use Windwalker\System\ExtensionHelper;

/**
 * Path Helper
 *
 * @since 2.0
 */
class PathHelper
{
	/**
	 * Get the path of extension.
	 *
	 * @param   string   $element   The extension element name, example: com_content or plg_group_name
	 * @param   string   $client    Site or administrator.
	 * @param   boolean  $absolute  True to return whole path.
	 *
	 * @return  string  The found path.
	 */
	public static function get($element, $client = null, $absolute = true)
	{
		$element = strtolower($element);

		$extracted = ExtensionHelper::extractElement($element);

		$extension = $extracted['type'];
		$name  = $extracted['name'];
		$group = $extracted['group'];

		// Assign name path.
		switch ($extension)
		{
			case 'component':
			case 'module':
				$folder = $element;
				break;

			case 'plugin':
				$folder = $group . '/' . $name;
				$client = 'site';
				break;

			case 'library':
				$client = 'site';

			default:
				$folder = $name;
				break;
		}

		// Build path
		$extension = Inflector::getInstance()->toPlural($extension);
		$path = $extension . '/' . $folder;

		if (!$absolute)
		{
			return $path;
		}

		// Add absolute path.
		switch ($client)
		{
			case 'site':
				$path = JPATH_SITE . '/' . $path;
				break;

			case 'admin':
			case 'administrator':
				$path = JPATH_ADMINISTRATOR . '/' . $path;
				break;

			default:
				$path = JPATH_BASE . '/' . $path;
				break;
		}

		return $path;
	}

	/**
	 * Get path of administrator.
	 *
	 * @param   string   $element   The extension element name, example: com_content or plg_group_name
	 * @param   boolean  $absolute  True to return whole path.
	 *
	 * @return  string  The found path.
	 */
	public static function getAdmin($element, $absolute = true)
	{
		return static::get($element, 'administrator', $absolute);
	}

	/**
	 * Get path of front-end.
	 *
	 * @param   string   $element   The extension element name, example: com_content or plg_group_name
	 * @param   boolean  $absolute  True to return whole path.
	 *
	 * @return  string  The found path.
	 */
	public static function getSite($element, $absolute = true)
	{
		return static::get($element, 'site', $absolute);
	}
}
