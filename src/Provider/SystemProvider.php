<?php

namespace Windwalker\Provider;

use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Windwalker\DI\ServiceProvider;
use Windwalker\Helper\DateHelper;

/**
 * Windwalker system provider.
 *
 * @since 2.0
 */
class SystemProvider extends ServiceProvider
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
		// Global Config
		$container->share('joomla.config', array('JFactory', 'getConfig'));

		// Windwalker Config
		$container->share('windwalker.config', array($this, 'loadConfig'));

		// Database
		$this->share($container, 'db', 'JDatabaseDriver', array('JFactory', 'getDbo'));

		// Language
		$this->share($container, 'language', 'JLanguage', array('JFactory', 'getLanguage'));

		// Dispatcher
		$this->share($container, 'event.dispatcher', 'JEventDispatcher', array('JEventDispatcher', 'getInstance'));

		// Date
		$this->set(
			$container,
			'date',
			'JDate',
			function()
			{
				return DateHelper::getDate();
			}
		);

		// Global
		$container->set('SplPriorityQueue',
			function()
			{
				return new \SplPriorityQueue;
			}
		);

		// Asset
		$container->share(
			'helper.asset',
			function()
			{
				return new \Windwalker\Helper\AssetHelper;
			}
		);

		// Detect deferent environment
		if (defined('WINDWALKER_CONSOLE'))
		{
			$container->registerServiceProvider(new CliProvider);
		}
		else
		{
			$container->registerServiceProvider(new WebProvider);
		}
	}

	/**
	 * Load config.
	 *
	 * @return  Registry Config registry object.
	 */
	public function loadConfig()
	{
		$file = WINDWALKER . '/config.json';

		if (!is_file($file))
		{
			\JFile::copy(WINDWALKER . '/config.dist.json', $file);
		}

		$config = new Registry;

		return $config->loadFile($file, 'json');
	}
}
