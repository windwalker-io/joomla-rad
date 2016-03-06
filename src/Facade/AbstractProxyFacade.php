<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Facade;

/**
 * The AbstractProxyFacade class.
 * 
 * @since  2.1
 */
abstract class AbstractProxyFacade extends AbstractFacade
{
	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param   string  $method  The method name.
	 * @param   array   $args    The arguments of method call.
	 *
	 * @return  mixed
	 */
	public static function __callStatic($method, $args)
	{
		$instance = static::getInstance();

		switch (count($args))
		{
			case 0:
				return $instance->$method();
			case 1:
				return $instance->$method($args[0]);
			case 2:
				return $instance->$method($args[0], $args[1]);
			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);
			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);
			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}
}
