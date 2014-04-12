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
 * Class Config
 *
 * @since 1.0
 */
abstract class ConfigHelper
{
	/**
	 * Property type.
	 *
	 * @var  string
	 */
	protected static $type = 'yaml';

	/**
	 * saveConfig
	 *
	 * @param \Joomla\Registry\Registry $config
	 * @param string                    $element
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
	 * setConfig
	 *
	 * @param \Joomla\Registry\Registry $config
	 * @param string                    $element
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
	 * getPath
	 *
	 * @param string $element
	 *
	 * @throws  \LogicException
	 * @return  string
	 */
	public static function getConfig($element)
	{
		/** @var $class AbstractConfig */
		$class = static::getClass($element);

		if (!is_subclass_of($class, 'Windwalker\\System\\Config\\ConfigInterface'))
		{
			throw new \LogicException(sprintf('Please make %s implement Windwalker\\Syatem\\Config\\ConfigInterface', $class));
		}

		return $class::getConfig();
	}

	/**
	 * getClass
	 *
	 * @param string $element
	 *
	 * @throws \LogicException
	 * @throws \DomainException
	 * @return  string
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
