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
abstract class ConfigHelper
{
	/**
	 * Property config.
	 *
	 * @var  Registry[]
	 */
	public static $config = array();

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
	 * @param                           $prefix
	 * @param string                    $extType
	 * @param null                      $group
	 *
	 * @return  void
	 */
	public static function saveConfig(Registry $config, $prefix, $extType = 'component', $group = null)
	{
		$class = static::getClass($prefix, $extType, $group);

		$class::saveConfig($config);
	}

	/**
	 * setConfig
	 *
	 * @param \Joomla\Registry\Registry $config
	 * @param string                    $prefix
	 * @param string                    $extType
	 * @param null                      $group
	 *
	 * @return  void
	 */
	public static function setConfig(Registry $config, $prefix, $extType = 'component', $group = null)
	{
		/** @var $class AbstractConfig */
		$class = static::getClass($prefix, $extType, $group);

		$class::setConfig($config);
	}

	/**
	 * getPath
	 *
	 * @param string $prefix
	 * @param string $extType
	 * @param string $group
	 *
	 * @throws  \LogicException
	 * @return  string
	 */
	public static function getConfig($prefix, $extType = 'component', $group = null)
	{
		$class = static::getClass($prefix, $extType, $group);

		if (!is_subclass_of($class, 'Windwalker\\Syatem\\Config\\AbstractConfig'))
		{
			throw new \LogicException(sprintf('Please make %s extends Windwalker\\Syatem\\Config\\AbstractConfig'));
		}

		return $class::getConfig();
	}

	/**
	 * getClass
	 *
	 * @param string $prefix
	 * @param string $extType
	 * @param string $group
	 *
	 * @return  string
	 *
	 * @throws \LogicException
	 * @throws \DomainException
	 */
	public static function getClass($prefix, $extType = 'component', $group = null)
	{
		switch ($extType)
		{
			case 'module':
				$class = 'Mod' . ucfirst($prefix) . '\\Config\\Config';
				break;

			case 'plugin':
				if (!$group)
				{
					throw new \LogicException(sprintf('Please give me group name when get plugin config.'));
				}

				$class = 'Plg' . ucfirst($group) . ucfirst($prefix) . '\\Config\\Config';
				break;

			case 'component':
				$class = ucfirst($prefix) . '\\Config\\Config';
				break;

			default:
				throw new \DomainException(sprintf('Do get config from this extension type: %s', $extType));
		}

		return $class;
	}
}
