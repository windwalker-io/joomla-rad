<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SystemBundle\Command\User\Create;

use Windwalker\Console\Command\Command;
use Windwalker\Console\Prompter\NotNullPrompter;
use Windwalker\Console\Prompter\PasswordPrompter;
use Windwalker\DataMapper\DataMapper;
use Windwalker\DataMapper\DataMapperFacade;
use Windwalker\Table\TableHelper;

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
	public function initialise()
	{
		$this->addGlobalOption('g')
			->alias('group')
			->description('The group id, default will use SuperUser group.');

		parent::initialise();
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
		\JFactory::getLanguage()->load('lib_joomla', JPATH_ROOT, 'en-GB');

		// Init user
		$this->initUser();
		
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

		$groupId = $this->getOption('group');

		if (!$groupId)
		{
			$groupId = $this->getSuperUserGroup();
		}

		// Save Super admin
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update('#__user_usergroup_map')
			->set('group_id = ' . $groupId)
			->where('user_id = ' . $userId);

		$db->setQuery($query)->execute();

		$this->out()->out('Create user success.');

		return true;
	}

	/**
	 * initUser
	 *
	 * @return  void
	 */
	protected function initUser()
	{
		$mapper = new DataMapper('#__users');
		$users = $mapper->findOne();

		if ($users->notNull())
		{
			return;
		}

		$helper = new TableHelper('#__users');
		$helper->initRow(mt_rand(50, 150));
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

	/**
	 * getSuperUserGroup
	 *
	 * @return  integer
	 */
	protected function getSuperUserGroup()
	{
		$data = DataMapperFacade::findOne('#__assets', 1);

		$rules = json_decode($data->rules, true);

		if (!isset($rules['core.admin']))
		{
			return static::SUPER_USER_GROUP_ID;
		}

		$ids = array_keys($rules['core.admin']);

		return array_shift($ids);
	}
}
