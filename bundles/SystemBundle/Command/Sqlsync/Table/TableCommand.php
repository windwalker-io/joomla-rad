<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Table;

use SystemBundle\Command\Sqlsync\Table\ListAll\ListAllCommand;
use SystemBundle\Command\Sqlsync\Table\Rename\RenameCommand;
use SystemBundle\Command\Sqlsync\Table\Status\StatusCommand;
use SystemBundle\Command\Sqlsync\Table\Sync\SyncCommand;
use SystemBundle\Command\Sqlsync\Table\Track\TrackCommand;
use Windwalker\Console\Command\Command;



/**
 * Class Table
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class TableCommand extends Command
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
	protected $name = 'table';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Model operation.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'table <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Initialise command.
	 *
	 * @return void
	 *
	 * @since  2.0
	 */
	protected function initialise()
	{
		$this->addCommand(new ListAllCommand);
//		$this->addCommand(new RenameCommand);
		$this->addCommand(new StatusCommand);
		$this->addCommand(new SyncCommand);
		$this->addCommand(new TrackCommand);
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
