<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Table\Status;


use Windwalker\Console\Command\Command;
use Windwalker\Sqlsync\Model\Table;
use Windwalker\Sqlsync\Model\Track;
use Windwalker\Helper\ArrayHelper;

/**
 * Class Status
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class StatusCommand extends Command
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
	protected $name = 'status';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Show tracking status.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'status <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$tableModel = new Table;

		$statuses = $tableModel->status();

		$tables = ArrayHelper::getColumn($statuses, 'table');

		$maxLength = max(array_map('strlen', $tables));

		// Show message
		$this->out()->out('Track Status:')->out();

		$titleSpaces = $maxLength - 5;

		$this->out(sprintf("TABLE NAME %-{$titleSpaces}s STATUS", ''));

		$this->out('---------------------------------------------------------------');

		// List table & status
		foreach ($statuses as $status)
		{
			$spaces = $maxLength - strlen($status['table']) + 4;

			$this->out(sprintf("- %s %-{$spaces}s %s", $status['table'], '', $status['status']));
		}
	}
}
