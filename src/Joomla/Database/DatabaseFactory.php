<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Joomla\Database;

use JDatabaseDriver as DatabaseDriver;
use Windwalker\DI\Container;

/**
 * Class DatabaseFactory
 *
 * @deprecated  3.0
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
	 *
	 * @deprecated  3.0
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
	 *
	 * @deprecated  3.0
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
	 *
	 * @deprecated  3.0
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
	 * @return  void
	 *
	 * @deprectaed  3.0
	 */
	public static function setCommand(DatabaseCommand $command)
	{
		self::$command = $command;
	}
}
