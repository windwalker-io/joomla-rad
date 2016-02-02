<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Script;

use Windwalker\DI\Container;
use Windwalker\Asset\AssetManager;

/**
 * The AbstractScriptManager class.
 *
 * @since  {DEPLOY_VERSION}
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
	 * inited
	 *
	 * @param   string $name
	 * @param   mixed  $data
	 *
	 * @return bool
	 */
	protected static function inited($name, $data = null)
	{
		$id = static::getInitedId($data);

		$class = get_called_class();

		if (!isset(static::$inited[$class][$name][$id]))
		{
			static::$inited[$class][$name][$id] = true;

			return false;
		}

		return true;
	}

	/**
	 * getInitedId
	 *
	 * @param   mixed  $data
	 *
	 * @return  string
	 */
	protected static function getInitedId($data)
	{
		return sha1(serialize($data));
	}

	/**
	 * Method to get property Asset
	 *
	 * @param string $option
	 *
	 * @return AssetManager
	 */
	public static function getAsset($option = 'windwalker')
	{
		if (!empty(static::$assets[$option]))
		{
			return static::$assets[$option];
		}

		$container = static::getContainer($option);

		if ($container->exists('helper.asset'))
		{
			$asset = $container->get('helper.asset');
		}
		else
		{
			$asset = new AssetManager($option);
		}

		return static::$assets[$option] = $asset;
	}

	/**
	 * Method to set property asset
	 *
	 * @param   string       $option
	 * @param   AssetManager $asset
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
}
