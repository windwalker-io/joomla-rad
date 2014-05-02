<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Debugger;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * The Debugger helper.
 *
 * @since 2.0
 */
abstract class Debugger
{
	/**
	 * Property whoops.
	 *
	 * @var Run
	 */
	static protected $whoops = null;

	/**
	 * Property handler.
	 *
	 * @var PrettyPageHandler
	 */
	static protected $handler = null;

	/**
	 * registerWhoops
	 *
	 * @return void
	 */
	public static function registerWhoops()
	{
		self::$whoops  = new Run;
		self::$handler = new PrettyPageHandler;

		self::$whoops->pushHandler(self::$handler);
		self::$whoops->register();
	}

	/**
	 * Add a data table to whoops.
	 *
	 * @param string $label The data table name.
	 * @param mixed  $data  The data to show.
	 *
	 * @return void
	 */
	public static function add($label, $data)
	{
		self::$handler->addDataTable($label . ':' . uniqid(), (array) $data);
	}
}
