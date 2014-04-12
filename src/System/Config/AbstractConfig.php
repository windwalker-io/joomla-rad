<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\System\Config;

use Joomla\Filesystem\File;
use Joomla\Registry\Registry;

/**
 * Class Config
 *
 * @since 1.0
 */
abstract class AbstractConfig implements ConfigInterface
{
	/**
	 * Property config.
	 *
	 * @var  Registry
	 */
	public static $config = null;

	/**
	 * Property type.
	 *
	 * @var  string
	 */
	protected static $type = 'json';

	/**
	 * get
	 *
	 * @param $name
	 * @param $default
	 *
	 * @return  mixed
	 */
	public static function get($name, $default = null)
	{
		return static::getConfig()->get($name, $default);
	}

	/**
	 * set
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return  mixed
	 */
	public static function set($name, $value)
	{
		return static::getConfig()->set($name, $value);
	}

	/**
	 * saveConfig
	 *
	 * @return  void
	 */
	public static function saveConfig()
	{
		File::write(static::getPath(), static::getConfig()->toString(static::$type));
	}

	/**
	 * getConfig
	 *
	 * @return  Registry
	 */
	public static function getConfig()
	{
		if (static::$config instanceof Registry)
		{
			return static::$config;
		}

		$config = with(new Registry)
			->loadFile(static::getPath(), static::$type);

		return static::$config = $config;
	}

	/**
	 * setConfig
	 *
	 * @param   \Joomla\Registry\Registry $config
	 *
	 * @return  AbstractConfig  Return self to support chaining.
	 */
	public static function setConfig(Registry $config)
	{
		self::$config = $config;
	}
}
 