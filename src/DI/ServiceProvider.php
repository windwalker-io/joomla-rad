<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\DI;

use Joomla\DI\ServiceProviderInterface;
use Joomla\DI\Container as JoomlaContainer;

/**
 * Basic ServiceProvider class.
 *
 * @since 2.0
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
	/**
	 * Set a object into container with alias.
	 *
	 * @param JoomlaContainer $container  The DI container.
	 * @param string          $alias      Alias name.
	 * @param string          $class      Class name.
	 * @param string          $value      The object or closure to aware this object.
	 *
	 * @return  \Joomla\DI\Container  Return self to support chaining.
	 */
	protected function set(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->set($class, $value);
	}

	/**
	 * Share a object into container with alias.
	 *
	 * @param JoomlaContainer $container  The DI container.
	 * @param string          $alias      Alias name.
	 * @param string          $class      Class name.
	 * @param string          $value      The object or closure to aware this object.
	 *
	 * @return  \Joomla\DI\Container  Return self to support chaining.
	 */
	protected function share(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->share($class, $value);
	}

	/**
	 * Set a shared object into container with alias.
	 *
	 * @param JoomlaContainer $container  The DI container.
	 * @param string          $alias      Alias name.
	 * @param string          $class      Class name.
	 * @param string          $value      The object or closure to aware this object.
	 *
	 * @return  \Joomla\DI\Container  Return self to support chaining.
	 */
	protected function singleton(JoomlaContainer $container, $alias, $class, $value)
	{
		return $this->share($container, $alias, $class, $value);
	}

	/**
	 * Set a protected object into container with alias.
	 *
	 * @param JoomlaContainer $container  The DI container.
	 * @param string          $alias      Alias name.
	 * @param string          $class      Class name.
	 * @param string          $value      The object or closure to aware this object.
	 *
	 * @return  \Joomla\DI\Container  Return self to support chaining.
	 */
	protected function protect(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->protect($class, $value);
	}

	/**
	 * Build an object and set it into container with alias.
	 *
	 * @param JoomlaContainer $container  The DI container.
	 * @param string          $alias      Alias name.
	 * @param string          $class      Class name.
	 * @param string          $value      The object or closure to aware this object.
	 *
	 * @return  \Joomla\DI\Container  Return self to support chaining.
	 */
	protected function buildObject(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->buildObject($class, $value);
	}

	/**
	 * Build an shared object and set it into container with alias.
	 *
	 * @param JoomlaContainer $container  The DI container.
	 * @param string          $alias      Alias name.
	 * @param string          $class      Class name.
	 * @param string          $value      The object or closure to aware this object.
	 *
	 * @return  \Joomla\DI\Container  Return self to support chaining.
	 */
	protected function buildSharedObject(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->buildSharedObject($class, $value);
	}
}
