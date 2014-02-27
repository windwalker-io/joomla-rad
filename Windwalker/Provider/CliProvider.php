<?php

namespace Windwalker\Provider;

use Joomla\Console\Output\Stdout;
use Joomla\DI\Container;
use Joomla\Input\Input;
use Windwalker\Console\Application\Console;
use Windwalker\DI\ServiceProvider;

/**
 * Class CliProvider
 *
 * @since 1.0
 */
class CliProvider extends ServiceProvider
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 *
	 * @since   1.0
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
	 * createConsole
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
