<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
