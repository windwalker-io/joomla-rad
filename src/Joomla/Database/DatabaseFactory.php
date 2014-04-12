<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Joomla\Database;

use JDatabaseDriver as DatabaseDriver;
use Windwalker\DI\Container;

/**
 * Class DatabaseFactory
 */
abstract class DatabaseFactory
{
	/**
	 * Property db.
	 *
	 * @var DatabaseDriver
	 */
	protected static $db = null;

	/**
	 * Property command.
	 *
	 * @var  DatabaseCommand
	 */
	protected static $command = null;

	/**
	 * getDbo
	 *
	 * @param array $option
	 * @param bool  $forceNew
	 *
	 * @return  DatabaseDriver
	 */
	public static function getDbo($option = array(), $forceNew = false)
	{
		return Container::getInstance()->get('db', $forceNew);
	}

	/**
	 * setDb
	 *
	 * @param   DatabaseDriver $db
	 *
	 * @return  void
	 */
	public static function setDbo(DatabaseDriver $db)
	{
		Container::getInstance()->share('db', $db);
	}

	/**
	 * getCommand
	 *
	 * @param bool $forceNew
	 *
	 * @return  DatabaseCommand
	 */
	public static function getCommand($forceNew = false)
	{
		if (!self::$command || $forceNew)
		{
			self::$command = new DatabaseCommand(static::getDbo());
		}

		return self::$command;
	}

	/**
	 * setCommand
	 *
	 * @param   DatabaseCommand $command
	 *
	 * @return  DatabaseFactory  Return self to support chaining.
	 */
	public static function setCommand(DatabaseCommand $command)
	{
		self::$command = $command;
	}
}
