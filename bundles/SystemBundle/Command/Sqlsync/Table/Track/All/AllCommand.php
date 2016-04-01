<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Table\Track\All;

use Windwalker\Console\Command\Command;
use Windwalker\Sqlsync\Model\Table;
use Windwalker\Sqlsync\Model\Track;



/**
 * Class All
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class AllCommand extends Command
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
	protected $name = 'all';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Track table column and data.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'all <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Property status.
	 *
	 * @var  string
	 */
	protected $status = 'all';

	/**
	 * Initialise command.
	 *
	 * @return void
	 *
	 * @since  2.0
	 */
	protected function initialise()
	{
		$this->addOption('a')
			->alias('all')
			->defaultValue(0)
			->description('Track all tables.');

		$this->addOption('s')
			->alias('site')
			->defaultValue(0)
			->description('Track all tables of this site.');
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$track = new Track;

		$args  = $this->io->getArguments();
		$all   = $this->getOption('a');
		$site  = $this->getOption('s');

		if (empty($args[0]) && !$all && !$site)
		{
			$this->out()->out('Missing argument 1 <table_name> or -a|--all, -s|--site.');

			return;
		}

		if ($all && $site)
		{
			$this->out()->out('Do you want to track tables of all or just this site?');

			return;
		}

		$tableObject = new Table;

		if ($all)
		{
			$tables = $tableObject->listAll();
		}
		elseif ($site)
		{
			$tables = $tableObject->listSite();
		}
		else
		{
			$tables = array($args[0]);
		}

		$track->setTrack($tables, $this->status);

		if (count($tables) > 1)
		{
			$name = count($tables) . ' tables';
		}
		else
		{
			$name = $tables[0];
		}

		$this->out()->out(sprintf('Set %s tracking %s.', $name, $this->status));
	}
}
