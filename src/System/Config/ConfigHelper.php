<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\System\Config;

use Joomla\Registry\Registry;
use Windwalker\System\ExtensionHelper;

/**
 * The config helper to get config from other extension build by windwalker.
 *
 * @since 2.0
 */
abstract class ConfigHelper
{
	/**
	 * The config file type.
	 *
	 * @var  string
	 */
	protected static $type = 'yaml';

	/**
	 * Save the config.
	 *
	 * @param Registry $config  The config object.
	 * @param string   $element The extension name.
	 *
	 * @return  void
	 */
	public static function saveConfig(Registry $config, $element)
	{
		/** @var $class AbstractConfig */
		$class = static::getClass($element);

		$class::saveConfig($config);
	}

	/**
	 * Set config.
	 *
	 * @param Registry $config  The config object.
	 * @param string   $element The extension name.
	 *
	 * @return  void
	 */
	public static function setConfig(Registry $config, $element)
	{
		/** @var $class AbstractConfig */
		$class = static::getClass($element);

		$class::setConfig($config);
	}

	/**
	 * Get config object.
	 *
	 * @param string $element The extension name.
	 *
	 * @throws  \LogicException
	 * @return  Registry The config object.
	 */
	public static function getConfig($element)
	{
		/** @var $class AbstractConfig */
		$class = static::getClass($element);

		if (!is_subclass_of($class, 'Windwalker\\System\\Config\\ConfigInterface'))
		{
			throw new \LogicException(
				sprintf(
					'Please make %s implement Windwalker\\Syatem\\Config\\ConfigInterface or Windwalker\\Syatem\\Config\\AbstractConfig',
					$class
				)
			);
		}

		return $class::getConfig();
	}

	/**
	 * Get config class name by extension name.
	 *
	 * Example:
	 * - Component - Flower\Config\Config
	 * - Module    - ModFlower\Config\Config
	 * - Plugin    - PlgSystemFlower\Config\Config
	 * - Template  - TplFlower\Config\Config
	 *
	 * @param string $element  The extension name.
	 *
	 * @throws \LogicException
	 * @throws \DomainException
	 * @return  string The config class name.
	 */
	public static function getClass($element)
	{
		$extracted = ExtensionHelper::extractElement($element);

		switch ($extracted['type'])
		{
			case 'module':
				$class = 'Mod' . ucfirst($extracted['name']) . '\\Config\\Config';
				break;

			case 'plugin':
				if (!$extracted['group'])
				{
					throw new \LogicException(sprintf('Please give me group name when get plugin config.'));
				}

				$class = 'Plg' . ucfirst($extracted['group']) . ucfirst($extracted['name']) . '\\Config\\Config';
				break;

			case 'component':
				$class = ucfirst($extracted['name']) . '\\Config\\Config';
				break;

			case 'template':
				$class = 'Tpl' . ucfirst($extracted['name']) . '\\Config\\Config';
				break;

			default:
				throw new \DomainException(sprintf('Don\'t get config from this extension: %s', $element));
		}

		return $class;
	}
}
