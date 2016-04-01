<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync;

/**
 * Class Factory
 */
abstract class Factory
{
	/**
	 * @var Config
	 */
	static public $config;

	/**
	 * getConfig
	 *
	 * @return Config
	 */
	public static function getConfig()
	{
		if (self::$config)
		{
			return self::$config;
		}

		$defaultConfig = __DIR__ . '/Resource/config.yml';

		$userConfig = JPATH_ROOT . '/tmp/sqlsync/config.yml';

		if (!file_exists($userConfig))
		{
			$content = '';

			\JFile::write($userConfig, $content);
		}

		$config = new Config;

		$config->loadFile($defaultConfig, 'yaml')
			->loadFile($userConfig, 'yaml');

		return self::$config = $config;
	}
}