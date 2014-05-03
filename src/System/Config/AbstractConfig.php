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
 * The config class.
 *
 * @since 2.0
 */
abstract class AbstractConfig implements ConfigInterface
{
	/**
	 * Config data cache.
	 *
	 * @var  Registry
	 */
	public static $config = null;

	/**
	 * Config file type.
	 *
	 * @var  string
	 */
	protected static $type = 'json';

	/**
	 * Get config.
	 *
	 * @param string $name    The config key name.
	 * @param mixed  $default The default value if not exists.
	 *
	 * @return  mixed Config value of this key.
	 */
	public static function get($name, $default = null)
	{
		return static::getConfig()->get($name, $default);
	}

	/**
	 * Set config.
	 *
	 * @param string $name  The config key name.
	 * @param mixed  $value The value of this key.
	 *
	 * @return  mixed Return from config object set() method.
	 */
	public static function set($name, $value)
	{
		return static::getConfig()->set($name, $value);
	}

	/**
	 * Save config to file.
	 *
	 * @return  void
	 */
	public static function saveConfig()
	{
		File::write(static::getPath(), static::getConfig()->toString(static::$type));
	}

	/**
	 * Get config from file. Will get from cache if has loaded.
	 *
	 * @return  Registry Config object.
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
	 * Set config object into this class.
	 *
	 * @param   \Joomla\Registry\Registry $config The config object.
	 *
	 * @return  void
	 */
	public static function setConfig(Registry $config)
	{
		self::$config = $config;
	}
}
