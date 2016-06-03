<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\System\Installer;

use Composer\Script\Event;

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
	 * @param Event $event The command event.
	 *
	 * @return  void
	 */
	public static function install(Event $event)
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
