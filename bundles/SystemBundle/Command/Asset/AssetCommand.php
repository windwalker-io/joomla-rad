<?php

namespace SystemBundle\Command\Asset;

use SystemBundle\Command\Asset\Makesum\MakesumCommand;
use Windwalker\Console\Command\Command;

/**
 * Class Asset
 *
 * @package Command
 */
class AssetCommand extends Command
{
	public $name = 'asset';

	public $description = 'Asset tools.';

	public static $isEnabled = true;

	protected function initialise()
	{
		$this->addCommand(new MakesumCommand);

		parent::initialise();
	}

	protected function doExecute()
	{
		return parent::doExecute();
	}
}
