<?php

namespace SystemBundle\Command\System;

use SystemBundle\Command\System\CleanCache\ClearCacheCommand;
use SystemBundle\Command\System\Off\OffCommand;
use SystemBundle\Command\System\On\OnCommand;
use Windwalker\Console\Command\Command;

class SystemCommand extends Command
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = 'system';

	/**
	 * Property description.
	 *
	 * @var  string
	 */
	protected $description = 'System control.';

	/**
	 * Initialise command.
	 *
	 * @return void
	 *
	 * @since  2.0
	 */
	protected function initialise()
	{
		$this->addCommand(new ClearCacheCommand);
		$this->addCommand(new OnCommand);
		$this->addCommand(new OffCommand);
	}
}
