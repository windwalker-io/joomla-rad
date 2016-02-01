<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Facade;

use Windwalker\DI\Container;

/**
 * The AbstractFacade class.
 * 
 * @since  {DEPLOY_VERSION}
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
	 * getInstance
	 *
	 * @param bool $forceNew
	 *
	 * @return mixed
	 */
	public static function getInstance($forceNew = false)
	{
		return static::getContainer()->get(static::getDIKey(), $forceNew);
	}

	/**
	 * getContainer
	 *
	 * @param string $child
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
	 * @param   string $key
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
	 * @param   string $child
	 *
	 * @return  void
	 */
	public static function setChildName($child)
	{
		static::$_child = $child;
	}
}
