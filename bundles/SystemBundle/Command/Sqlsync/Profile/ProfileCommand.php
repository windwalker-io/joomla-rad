<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Profile;

use SystemBundle\Command\Sqlsync\Profile\Add\AddCommand;
use SystemBundle\Command\Sqlsync\Profile\Checkout\CheckoutCommand;
use SystemBundle\Command\Sqlsync\Profile\Remove\RemoveCommand;
use Windwalker\Console\Command\Command;
use Windwalker\Sqlsync\Model\Profile\ProfilesModel;



/**
 * Class Profile
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class ProfileCommand extends Command
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
	protected $name = 'profile';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Profiles.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'profile <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Initialise command.
	 *
	 * @return void
	 *
	 * @since  2.0
	 */
	protected function initialise()
	{
		$this->addCommand(new AddCommand);
		$this->addCommand(new CheckoutCommand);
		$this->addCommand(new RemoveCommand);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$model = new ProfilesModel;

		$profiles = $model->getItems();

		$this->out();

		foreach ($profiles as $profile)
		{
			$this->out(($profile->is_current ? '*' : ' ') . ' ', false);

			$this->out($profile->title);
		}

		return true;
	}
}
