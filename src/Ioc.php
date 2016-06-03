<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker;

use Joomla\Registry\Registry;
use Windwalker\Asset\AssetManager;
use Windwalker\Console\IO\IO;
use Windwalker\DI\Container;

/**
 * The Ioc class.
 *
 * @since  2.1.5
 */
class Ioc
{
	/**
	 * getApplication
	 *
	 * @param string $name
	 *
	 * @return  \JApplicationCms
	 */
	public static function getApplication($name = 'windwalker')
	{
		return static::get('app', false, $name);
	}

	/**
	 * getConfig
	 *
	 * @param string $name
	 *
	 * @return  Registry
	 */
	public static function getConfig($name = 'windwalker')
	{
		return static::get('joomla.config', false, $name);
	}

	/**
	 * getInput
	 *
	 * @param string $name
	 *
	 * @return  \JInput
	 */
	public static function getInput($name = 'windwalker')
	{
		return static::get('input', false, $name);
	}

	/**
	 * getLanguage
	 *
	 * @param string $name
	 *
	 * @return  \JLanguage
	 */
	public static function getLanguage($name = 'windwalker')
	{
		return static::get('language', false, $name);
	}

	/**
	 * getDocument
	 *
	 * @param string $name
	 *
	 * @return  \JDocument|\JDocumentHtml
	 */
	public static function getDocument($name = 'windwalker')
	{
		return static::get('document', false, $name);
	}

	/**
	 * getDbo
	 *
	 * @param string $name
	 *
	 * @return  \JDatabaseDriver
	 */
	public static function getDbo($name = 'windwalker')
	{
		return static::get('db', false, $name);
	}

	/**
	 * getSession
	 *
	 * @param string $name
	 *
	 * @return  \JSession
	 */
	public static function getSession($name = 'windwalker')
	{
		return static::get('session', false, $name);
	}

	/**
	 * getDispatcher
	 *
	 * @param string $name
	 *
	 * @return  \JEventDispatcher
	 */
	public static function getDispatcher($name = 'windwalker')
	{
		return static::get('event.dispatcher', false, $name);
	}

	/**
	 * getMailer
	 *
	 * @param string $name
	 *
	 * @return  \JMail
	 */
	public static function getMailer($name = 'windwalker')
	{
		return static::get('mailer', false, $name);
	}

	/**
	 * getAsset
	 *
	 * @param string $name
	 *
	 * @return  AssetManager
	 */
	public static function getAsset($name = 'windwalker')
	{
		return AssetManager::getInstance($name);
	}

	/**
	 * getIO
	 *
	 * @param string $name
	 *
	 * @return  IO
	 */
	public static function getIO($name = 'windwalker')
	{
		return static::get('io', false, $name);
	}

	/**
	 * get
	 *
	 * @param string $key
	 * @param bool   $forceNew
	 * @param string $name
	 *
	 * @return  mixed
	 */
	public static function get($key, $forceNew = false, $name = 'windwalker')
	{
		return static::factory($name)->get($key, $forceNew);
	}

	/**
	 * factory
	 *
	 * @param string $name
	 *
	 * @return  Container
	 */
	public static function factory($name = 'windwalker')
	{
		return Container::getInstance($name);
	}
}
