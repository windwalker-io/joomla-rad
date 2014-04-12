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
 * Class ServiceProvider
 *
 * @since 1.0
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
	/**
	 * set
	 *
	 * @param JoomlaContainer $container
	 * @param string          $alias
	 * @param string          $class
	 * @param string          $value
	 *
	 * @return  \Joomla\DI\JoomlaContainer
	 */
	protected function set(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->set($class, $value);
	}

	/**
	 * set
	 *
	 * @param JoomlaContainer $container
	 * @param string          $alias
	 * @param string          $class
	 * @param string          $value
	 *
	 * @return  \Joomla\DI\JoomlaContainer
	 */
	protected function share(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->share($class, $value);
	}

	/**
	 * set
	 *
	 * @param JoomlaContainer $container
	 * @param string          $alias
	 * @param string          $class
	 * @param string          $value
	 *
	 * @return  \Joomla\DI\JoomlaContainer
	 */
	protected function singleton(JoomlaContainer $container, $alias, $class, $value)
	{
		return $this->share($container, $alias, $class, $value);
	}

	/**
	 * set
	 *
	 * @param JoomlaContainer $container
	 * @param string          $alias
	 * @param string          $class
	 * @param string          $value
	 *
	 * @return  \Joomla\DI\JoomlaContainer
	 */
	protected function protect(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->protect($class, $value);
	}

	/**
	 * buildObject
	 *
	 * @param JoomlaContainer $container
	 * @param string          $alias
	 * @param string          $class
	 * @param string          $value
	 *
	 * @return  mixed
	 */
	protected function buildObject(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->buildObject($class, $value);
	}

	/**
	 * buildObject
	 *
	 * @param JoomlaContainer $container
	 * @param string          $alias
	 * @param string          $class
	 * @param string          $value
	 *
	 * @return  mixed
	 */
	protected function buildSharedObject(JoomlaContainer $container, $alias, $class, $value)
	{
		return $container->alias($alias, $class)
			->buildSharedObject($class, $value);
	}
}
