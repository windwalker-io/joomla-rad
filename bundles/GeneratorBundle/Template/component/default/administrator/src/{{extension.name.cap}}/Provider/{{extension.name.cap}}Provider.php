<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace {{extension.name.cap}}\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// No direct access
defined('_JEXEC') or die;

/**
 * Class ServiceProvider
 *
 * @since 1.0
 */
class {{extension.name.cap}}Provider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 */
	public function register(Container $container)
	{
	}
}
