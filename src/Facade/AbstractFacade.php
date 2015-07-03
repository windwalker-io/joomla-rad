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
abstract class AbstractFacade implements FacadeInterface
{
	/**
	 * Property key.
	 *
	 * @var  string
	 */
	protected static $key;

	/**
	 * Property child.
	 *
	 * @var  string
	 */
	protected static $child;

	/**
	 * getInstance
	 *
	 * @return  mixed
	 */
	public static function getInstance()
	{
		return static::getContainer()->get(static::getDIKey());
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
		return Container::getInstance($child ? : static::$child);
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
		static::$key = $key;
	}

	/**
	 * Method to get property Child
	 *
	 * @return  string
	 */
	public static function getChildName()
	{
		return static::$child;
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
		static::$child = $child;
	}
}
