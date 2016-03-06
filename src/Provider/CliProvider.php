<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Provider;

use Joomla\DI\Container;
use Windwalker\Console\Application\Console;
use Windwalker\Console\IO\IO;
use Windwalker\DI\ServiceProvider;

/**
 * Provider for Console application.
 *
 * @since 2.0
 */
class CliProvider extends ServiceProvider
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
		// Application
		$this->share($container, 'app', 'Windwalker\Console\Application\Console', array($this, 'createConsole'));

		// Input
		$container->alias('io', 'Windwalker\Console\IO\IO')
			->alias('input', 'Windwalker\Console\IO\IO')
			->share('Windwalker\Console\IO\IO',
				function()
				{
					return new IO;
				}
			);
	}

	/**
	 * Create console application.
	 *
	 * @param Container $container
	 *
	 * @return  Console
	 */
	public function createConsole(Container $container)
	{
		return new Console($container->get('io'), $container->get('windwalker.config'));
	}
}
