<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Component;

use Windwalker\Helper\PathHelper;
use Windwalker\Object\Object;

/**
 * Class ComponentHelper
 *
 * @since 1.0
 */
class ComponentHelper
{
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   \JUser  $user       The user object.
	 * @param   string  $option     The component option.
	 * @param   string  $assetName  The asset name
	 * @param   integer $categoryId The category ID.
	 * @param   integer $id         The item ID.
	 *
	 * @return  Object
	 */
	public static function getActions(\JUser $user, $option, $assetName, $categoryId = 0, $id = 0)
	{
		$result	= new Object;

		$path = PathHelper::getAdmin($option) . '/etc/access.xml';

		if (!is_file($path))
		{
			$path = PathHelper::getAdmin($option) . '/access.xml';
		}

		if (!$id && !$categoryId)
		{
			$section = 'component';
		}
		elseif (!$id && $categoryId)
		{
			$section = 'category';
			$assetName .= '.category.' . (int) $categoryId;
		}
		elseif ($id && !$categoryId)
		{
			$section = $assetName;
			$assetName .= '.' . $assetName . '.' . $id;
		}
		else
		{
			$section = $assetName;
			$assetName .= '.' . $assetName;
		}

		$actions = \JAccess::getActionsFromFile($path, "/access/section[@name='" . $section . "']/");

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
