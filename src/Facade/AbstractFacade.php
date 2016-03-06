<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Facade;

use Windwalker\DI\Container;

/**
 * The AbstractFacade class.
 * 
 * @since  2.1
 */
abstract class AbstractFacade
{
	/**
	 * Property key.
	 *
	 * @var  string
	 */
	protected static $_key;

	/**
	 * Property child.
	 *
	 * @var  string
	 */
	protected static $_child;

	/**
	 * Get instance of the key.
	 *
	 * @param   boolean  $forceNew  Force new or not.
	 *
	 * @return  mixed  The value get from container.
	 */
	public static function getInstance($forceNew = false)
	{
		return static::getContainer()->get(static::getDIKey(), $forceNew);
	}

	/**
	 * Get DI contaier.
	 *
	 * @param   string  $child  The container child name.
	 *
	 * @return  Container
	 */
	public static function getContainer($child = null)
	{
		return Container::getInstance($child ? : static::$_child);
	}

	/**
	 * Method to set property key
	 *
	 * @param   string  $key  Set DI key to support Facade pattern.
	 *
	 * @return  void
	 */
	public static function setDIKey($key)
	{
		static::$_key = $key;
	}

	/**
	 * Method to get property Key
	 *
	 * @return  string
	 */
	public static function getDIKey()
	{
		return static::$_key;
	}

	/**
	 * Method to get property Child
	 *
	 * @return  string
	 */
	public static function getChildName()
	{
		return static::$_child;
	}

	/**
	 * Method to set property child
	 *
	 * @param   string  $child  Child name to get container.
	 *
	 * @return  void
	 */
	public static function setChildName($child)
	{
		static::$_child = $child;
	}
}
