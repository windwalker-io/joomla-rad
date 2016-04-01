<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\User\Create;

use Windwalker\Console\Command\Command;
use JConsole\Prompter\NotNullPrompter;
use Joomla\Console\Prompter\PasswordPrompter;



/**
 * Class Install
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class CreateCommand extends Command
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
	protected $name = 'create';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Create User profile';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'create <option>[option]</option>';

	/**
	 * Superuser group id.
	 *
	 * @var  int
	 */
	const SUPER_USER_GROUP_ID = 8;

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		// $this->addCommand();

		parent::configure();
	}

	/**
	 * Execute this command.
	 *
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return int
	 */
	protected function doExecute()
	{
		// Install User
		$userdata = array();

		$userdata['username'] = $this->with(new NotNullPrompter)->ask('Please enter account: ');

		$userdata['name'] = $this->with(new NotNullPrompter)->ask('Please enter user name: ');

		$userdata['email'] = $this->with(new NotNullPrompter)->ask('Please enter your email: ');

		$userdata['password'] = $this->with(new PasswordPrompter)->ask('Please enter password: ');

		$userdata['password2'] = $this->with(new PasswordPrompter)->ask('Please valid password: ');

		if ($userdata['password'] != $userdata['password2'])
		{
			throw new \InvalidArgumentException('ERROR: Password not matched.');
		}

		$userdata['groups'] = array(1);

		$userdata['block'] = 0;

		$userdata['sendEmail'] = 1;

		$user = new \JUser;

		if (!$user->bind($userdata))
		{
			throw new \RuntimeException($user->getError());
		}

		if (!$user->save())
		{
			throw new \RuntimeException($user->getError());
		}

		$userId = $user->id;

		// Save Super admin
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update('#__user_usergroup_map')
			->set('group_id = ' . self::SUPER_USER_GROUP_ID)
			->where('user_id = ' . $userId);

		$db->setQuery($query)->execute();

		$this->out()->out('Create user success.');

		return true;
	}

	/**
	 * For php 5.3 B/C.
	 *
	 * @param object $object
	 *
	 * @return  object
	 */
	public function with($object)
	{
		return $object;
	}
}
