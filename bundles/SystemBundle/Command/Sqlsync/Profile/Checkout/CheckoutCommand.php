<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\Sqlsync\Profile\Checkout;

use Windwalker\Console\Command\Command;
use Windwalker\Sqlsync\Model\Profile\ProfileModel;



/**
 * Class Checkout
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class CheckoutCommand extends Command
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
	protected $name = 'checkout';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Checkout to a profile.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'checkout <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$name = $this->getArgument(0);

		if (!$name)
		{
			throw new \Exception('Please enter a profile name.');
		}

		$model = new ProfileModel;

		$model->checkout($name);

		$this->out()->out(sprintf('Checked out to profile "%s".', $name));
	}
}
