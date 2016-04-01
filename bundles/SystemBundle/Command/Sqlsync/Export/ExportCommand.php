<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Export;


use Windwalker\Console\Command\Command;
use Windwalker\Console\Prompter\BooleanPrompter;
use Windwalker\Sqlsync\Factory;
use Windwalker\Sqlsync\Helper\ProfileHelper;
use Windwalker\Sqlsync\Model\Schema;



/**
 * Class Export
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class ExportCommand extends Command
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
	protected $name = 'export';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Export sql.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'export <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Initialise command.
	 *
	 * @return void
	 *
	 * @since  2.0
	 */
	protected function initialise()
	{
		$this->addOption('f')
			->alias('force')
			->defaultValue(0)
			->description('Force import all, ignore compare.');

		$this->addOption('a')
			->alias('all')
			->defaultValue(0)
			->description('All profiles');
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$type = $this->getOption('s') ? 'sql' : 'yaml';

		$model = new Schema;

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

			// Check schema.
			$path = $model->getPath($type);

			if (file_exists($path) && !$this->getOption('y'))
			{
				$prompter = new BooleanPrompter(
					sprintf('Schema file <info>%s</info> exists, do you want to override it? [Y/n]: ', $profile)
				);

				if (!$prompter->ask())
				{
					$this->out('cancelled.');

					return;
				}
			}

			// Exporting
			$this->out()->out('<comment>Exporting</comment> profile schema: <info>' . $profile . '</info>');

			$model->export($type);

			$this->out()->out(sprintf('Schema file dumped to: <info>%s</info>', $model->getState()->get('dump.path')));
		}

		/*
		$yaml = $this->getOption('yaml');

		$exporter = $yaml ? new YamlExporter : new SqlExporter;

		$result = $exporter->export();

		$config = \JFactory::getConfig();

		$file = 'site-' . $config->get('db') . '-' . date('Y-m-d-H-i-s');

		if ($yaml)
		{
			$file .= '.yml';
		}
		else
		{
			$file .= '.sql';
		}

		$file = JCONSOLE . '/jconsole/resource/sql/export/' . ($yaml ? 'yaml/' : '') . $file;

		\JFile::write($file, $result);

		$this->out()->out(sprintf('Sql file dumped to: %s', $file));
		*/
	}
}
