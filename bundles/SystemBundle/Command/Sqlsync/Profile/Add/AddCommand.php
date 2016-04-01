<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Profile\Add;

use Windwalker\Console\Command\Command;
use Windwalker\Sqlsync\Model\Profile\ProfileModel;



/**
 * Class Add
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class AddCommand extends Command
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
	protected $name = 'add';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Add a new profile';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'add <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$name = $this->getArgument(0);

		if (!$name)
		{
			throw new \Exception('Please enter a profile name.');
		}

		$model = new ProfileModel;

		$model->add($name);

		$this->out()->out(sprintf("Profile \"%s\" created.", $name));

		return true;
	}
}
