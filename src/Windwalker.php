<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker;

use Windwalker\DI\Container;

/**
 * Windwalker main application.
 *
 * @since 2.0
 */
class Windwalker
{
	/**
	 * Init windalkwer.
	 *
	 * @throws \Exception
	 * @return  void
	 */
	public function init()
	{
		$version = new \JVersion;

		if (!$version->isCompatible('3.2'))
		{
			throw new \Exception('Windwalker need Joomla! 3.2 or higher.');
		}

		// Import Windwalker autoload.
		$this->autoload();

		include_once __DIR__ . '/PHP/methods.php';

		define('WINDWALKER', dirname(__DIR__));

		define('WINDWALKER_SOURCE', __DIR__);

		define('WINDWALKER_BUNDLE', dirname(WINDWALKER) . '/windwalker-bundles');

		// Register global provider
		$container = Container::getInstance();

		$container->registerServiceProvider(new \Windwalker\Provider\SystemProvider);

		// Register bundles
		$this->registerBundles($container);

		// Load language
		$lang = $container->get('language');
		$lang->load('lib_windwalker', JPATH_BASE, null, false, false)
		|| $lang->load('lib_windwalker', WINDWALKER, null, false, false)
		|| $lang->load('lib_windwalker', JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load('lib_windwalker', WINDWALKER, $lang->getDefault(), false, false);
	}

	/**
	 * Register Bundles
	 *
	 * @param Container $container DI container.
	 *
	 * @return  void
	 */
	protected function registerBundles(Container $container)
	{
		if (! is_dir(WINDWALKER_BUNDLE))
		{
			return;
		}

		$paths = new \Windwalker\Filesystem\Path\PathCollection(
			array(
				WINDWALKER . '/bundles',
				WINDWALKER_BUNDLE,
			)
		);

		$bundles = $paths->findAll('Bundle$');

		$config = $container->get('windwalker.config');

		foreach ($bundles as $bundle)
		{
			$bundleName = $bundle->getBasename();

			$class = $bundleName . '\\' . $bundleName;

			\JLoader::registerNamespace($bundleName, dirname((string) $bundle));

			if (class_exists($class) && is_subclass_of($class, 'Windwalker\\Bundle\\AbstractBundle'))
			{
				$config->set('bundle.' . $bundleName, $class);

				$class::registerProvider($container);
			}
		}
	}

	/**
	 * Set autoload.
	 *
	 * @return  void
	 */
	public function autoload()
	{
		// Load Composer
		include_once dirname(__DIR__) . '/vendor/autoload.php';

		// Load Joomla framework
		\JLoader::registerNamespace('Joomla', JPATH_LIBRARIES . '/framework');

		// Load Windwalker framework
		\JLoader::registerNamespace('Windwalker', dirname(__DIR__));

		// Load some file out of nameing standard
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
	}
}
