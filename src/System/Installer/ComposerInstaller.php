<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\System\Installer;

use Composer\Script\CommandEvent;

/**
 * The composer installer.
 *
 * @since 1.0
 */
class ComposerInstaller
{
	/**
	 * Do install.
	 *
	 * @param CommandEvent $event The command event.
	 *
	 * @return  void
	 */
	public static function install(CommandEvent $event)
	{
		$windPath = getcwd();
		$root = realpath($windPath . '/../..');

		$io = $event->getIO();

		// Create console file.
		$io->write('Writing console file to bin.');

		WindwalkerInstaller::createBinFile($root);

		// Config file
		$io->write('Prepare config file.');

		WindwalkerInstaller::copyConfigFile($root);

		// Bundles dir
		$bundlesDir = dirname($windPath) . '/windwalker-bundles';

		if (WindwalkerInstaller::createBundleDir($root))
		{
			$io->write('Create bundle folder: ' . $bundlesDir);
		}

		// Complete
		$io->write('Install complete.');
	}
}
