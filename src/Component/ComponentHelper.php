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
use Windwalker\System\ExtensionHelper;

/**
 * Component Helper class.
 *
 * @since 2.0
 */
abstract class ComponentHelper
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
			$assetName .= '.category.' . $categoryId;
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

	/**
	 * Execute Component.
	 *
	 * @param string $option Component option name.
	 * @param string $client `admin` or `site`.
	 * @param array  $input  Input object.
	 *
	 * @return  mixed
	 */
	public static function executeComponent($option, $client = 'site', $input = array())
	{
		$element = ExtensionHelper::extractElement($option);
		$input = new \JInput($input);

		// Prevent class conflict
		class_alias('JString', 'Joomla\\String\\String');

		if (! defined('JPATH_COMPONENT_ADMINISTRATOR'))
		{
			define('JPATH_COMPONENT_ADMINISTRATOR', PathHelper::get($option, 'admin'));
			define('JPATH_COMPONENT_SITE', PathHelper::get($option, 'site'));
			define('JPATH_COMPONENT', PathHelper::get($option, $client));
		}

		$_SERVER['HTTP_HOST'] = 'windwalker';

		if ($client == 'admin')
		{
			$client = 'administrator';
		}

		$appClass = 'JApplication' . ucfirst($client);

		$console = \JFactory::$application;

		\JFactory::$application = $appClass::getInstance('site', $input);

		$class = ucfirst($element['name']) . 'Component';

		$component = new $class(ucfirst($element['name']), $input, \JFactory::$application);

		$result = $component->execute();

		\JFactory::$application = $console;

		return $result;
	}
}
