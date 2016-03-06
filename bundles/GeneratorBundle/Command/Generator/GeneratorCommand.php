<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Command\Generator;

use GeneratorBundle\Command\Generator\Add\AddCommand;
use GeneratorBundle\Command\Generator\Convert\ConvertCommand;
use GeneratorBundle\Command\Generator\Init\InitCommand;
use GeneratorBundle\Command\Generator\Test\TestCommand;
use Windwalker\Console\Command\Command;

defined('WINDWALKER') or die;

/**
 * Class Genarator
 *
 * @since  2.0
 */
class GeneratorCommand extends Command
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
	protected $name = 'generator';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Extension generator.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'generator <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function initialise()
	{
		parent::initialise();

		$this->addCommand(new InitCommand);
		$this->addCommand(new ConvertCommand);
		$this->addCommand(new AddCommand);
		$this->addCommand(new TestCommand);

		$this->addGlobalOption('c')
			->alias('client')
			->description('Site or administrator (admin)');

		$this->addGlobalOption('t')
			->alias('tmpl')
			->defaultValue('default')
			->description('Using template.');
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
