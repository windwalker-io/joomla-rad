<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Table\Rename;

use Windwalker\Console\Command\Command;



/**
 * Class Rename
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class RenameCommand extends Command
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
	protected $name = 'rename';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Rename a table.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'rename <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		if (isset($this->input->args[0]))
		{
			$this->out()->out('Missing argument 1 (Model name).');
		}

		$name = $this->input->args[0];

		if (isset($this->input->args[1]))
		{
			$this->out()->out('Missing argument 1 (New name).');
		}

		$newname = $this->input->args[1];


	}
}
