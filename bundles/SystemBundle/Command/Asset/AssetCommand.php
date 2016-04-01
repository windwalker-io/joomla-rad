<?php

namespace SystemBundle\Command\Asset;

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
}
