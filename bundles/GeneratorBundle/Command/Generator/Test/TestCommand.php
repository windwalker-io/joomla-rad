<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Command\Generator\Test;

use GeneratorBundle\Controller\GeneratorController;
use GeneratorBundle\Controller\TestGeneratorController;
use Windwalker\Console\Command\Command;

defined('WINDWALKER') or die;

/**
 * Class Init
 *
 * @since  2.0
 */
class TestCommand extends Command
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
	protected $name = 'test';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Generate test cases.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'test <cmd><package></cmd> <cmd><class></cmd> <cmd><target></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function initialise()
	{
		$this->help(
			<<<HELP
Example:

generator test Helper PathHelper
    Generate to: test/Helper/PathHelper.php

generator test Helper PathHelper Foo/Bar/PathHelper
    Generate to: test/Foo/Bar/PathHelper.php

HELP

		);

		parent::initialise();
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$generator = new TestGeneratorController($this);

		$generator->setTask('test')->execute();
	}
}
