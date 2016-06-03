<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Script;

use Windwalker\DI\Container;
use Windwalker\Asset\AssetManager;

/**
 * The AbstractScriptManager class.
 *
 * @since  2.1
 */
abstract class AbstractScriptManager
{
	/**
	 * Property inited.
	 *
	 * @var  array
	 */
	protected static $inited = array();

	/**
	 * Property asset.
	 *
	 * @var  AssetManager
	 */
	protected static $assets;

	/**
	 * Check an identify initialised of not.
	 *
	 * @param   string $name   the identify name, often use __METHOD__ as unique name.
	 * @param   mixed  $state  The state to combine into identify, you can use func_get_args() to add arguments
	 *                         to make sure the script only call once for every state.
	 *
	 * @return  boolean  A state inited or not.
	 */
	protected static function inited($name, $state = null)
	{
		$id = static::getInitedId($state);

		$class = get_called_class();

		if (!isset(static::$inited[$class][$name][$id]))
		{
			static::$inited[$class][$name][$id] = true;

			return false;
		}

		return true;
	}

	/**
	 * Get initialised identify.
	 *
	 * @param   mixed  $state  The data to check state is same.
	 *
	 * @return  string  The identify string to check state same.
	 */
	protected static function getInitedId($state)
	{
		return sha1(serialize($state));
	}

	/**
	 * Method to get AssetManager.
	 *
	 * @param  string  $option  The option name to get AssetManager.
	 *
	 * @return  AssetManager  The AssetManager object.
	 */
	public static function getAsset($option = 'windwalker')
	{
		if (!empty(static::$assets[$option]))
		{
			return static::$assets[$option];
		}

		return static::$assets[$option] = AssetManager::getInstance($option);
	}

	/**
	 * Method to set property asset
	 *
	 * @param   string        $option  The option name to set AssetManager.
	 * @param   AssetManager  $asset   The AssetManager object.
	 *
	 * @return  void
	 */
	public static function setAsset($option, AssetManager $asset)
	{
		static::$assets[$option] = $asset;
	}

	/**
	 * getContainer
	 *
	 * @param string $option
	 *
	 * @return Container
	 */
	protected static function getContainer($option = 'windwalker')
	{
		return Container::getInstance($option);
	}

	/**
	 * Reset state identify.
	 *
	 * @param  boolean  $all  Reset all state or only for this class.
	 *
	 * @return void
	 */
	public static function reset($all = false)
	{
		$class = get_called_class();

		if ($all || $class == __CLASS__)
		{
			static::$inited = array();
		}
		else
		{
			static::$inited[$class] = array();
		}
	}
}
