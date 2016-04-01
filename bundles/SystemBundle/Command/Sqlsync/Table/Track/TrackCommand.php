<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Table\Track;

use SystemBundle\Command\Sqlsync\Table\Track\All\AllCommand;
use SystemBundle\Command\Sqlsync\Table\Track\Cols\ColsCommand;
use SystemBundle\Command\Sqlsync\Table\Track\None\NoneCommand;
use Windwalker\Console\Command\Command;



/**
 * Class Track
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class TrackCommand extends Command
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
	protected $name = 'track';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Set track status of table.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'track <cmd>[all|cols|none]</cmd> <comment><table_name></comment> <option>[option]</option>';

	/**
	 * Initialise command.
	 *
	 * @return void
	 *
	 * @since  2.0
	 */
	protected function initialise()
	{
		$this->addCommand(new AllCommand);
		$this->addCommand(new ColsCommand);
		$this->addCommand(new NoneCommand);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		return parent::doExecute();
	}
}
