<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Provider;

use Joomla\Console\Output\Stdout;
use Joomla\DI\Container;
use Joomla\Input\Input;
use Windwalker\Console\Application\Console;
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
		$this->share($container, 'app', 'Windwalker\\Console\\Application\\Console', array($this, 'createConsole'));

		// Input
		$container->alias('input', 'Joomla\\Input\\Cli')
			->buildSharedObject('Joomla\\Input\\Cli');
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
		return new Console(null, $container->get('windwalker.config'), new Stdout);
	}
}
