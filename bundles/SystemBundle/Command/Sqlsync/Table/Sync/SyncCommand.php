<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Table\Sync;

use Windwalker\Console\Command\Command;
use Windwalker\Sqlsync\Helper\ProfileHelper;
use Windwalker\Sqlsync\Model\Table;
use Windwalker\Sqlsync\Factory;



/**
 * Class Sync
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class SyncCommand extends Command
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Console(Argument) name.
	 *
	 * @var  string
	 */
	protected $name = 'sync';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Sync tracking config.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'sync <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->addOption(
			array('a', 'all'),
			0,
			'All profiles'
		);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$tableModel = new Table;

		if ($this->getOption('a'))
		{
			$profiles = ProfileHelper::getAllProfiles();
		}
		else
		{
			$profiles = $this->io->getArguments() ? : array(ProfileHelper::getProfile());
		}

		$config = Factory::getConfig();

		foreach ($profiles as $profile)
		{
			$config->set('profile', $profile);

			$tableModel->sync();

			$path = $tableModel->getState()->get('track.save.path');

			$this->out()->out('Sync all tracking status to: ' . $path);
		}

		return;
	}
}
