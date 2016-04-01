<?php

namespace SystemBundle\Command\Sqlsync;

use SystemBundle\Command\Sqlsync\Backup\BackupCommand;
use SystemBundle\Command\Sqlsync\Column\ColumnCommand;
use SystemBundle\Command\Sqlsync\Export\ExportCommand;
use SystemBundle\Command\Sqlsync\Import\ImportCommand;
use SystemBundle\Command\Sqlsync\Profile\ProfileCommand;
use SystemBundle\Command\Sqlsync\Restore\RestoreCommand;
use SystemBundle\Command\Sqlsync\Table\TableCommand;
use Windwalker\Console\Command\Command;
use Joomla\Console\Option\Option;
use Windwalker\Sqlsync\Schema;
use Symfony\Component\Yaml\Dumper as SymfonyYamlDumper;

class SqlsyncCommand extends Command
{
	public $name = 'sql';

	public $description = 'SQL migration tools.';

	public static $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	/**
	 * Initialise command.
	 *
	 * @return void
	 *
	 * @since  2.0
	 */
	protected function initialise()
	{
		$this->addCommand(new BackupCommand);
		$this->addCommand(new ColumnCommand);
		$this->addCommand(new ExportCommand);
		$this->addCommand(new ImportCommand);
		$this->addCommand(new ProfileCommand);
		$this->addCommand(new RestoreCommand);
		$this->addCommand(new TableCommand);

		$this->addGlobalOption('y')
			->alias('assume-yes')
			->defaultValue(0)
			->description('Ignore confirm prompter.');
	}

	/**
	 * execute
	 *
	 * @return  mixed
	 */
	public function execute()
	{
		define('SQLSYNC_COMMAND',  __DIR__);

		define('SQLSYNC_RESOURCE', JPATH_ROOT . '/resources/sqlsync');

		define('SQLSYNC_PROFILE',  SQLSYNC_RESOURCE);

		define('SQLSYNC_LIB',      JPATH_LIBRARIES . '/windwalker/src/Sqlsync');

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		return parent::execute();
	}
}
