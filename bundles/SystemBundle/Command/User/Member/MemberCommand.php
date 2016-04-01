<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\User\Member;

use Windwalker\Console\Command\Command;



/**
 * Class Member
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class MemberCommand extends Command
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
	protected $name = 'member-pass-convert';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Member operator';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'member-pass-convert <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('*')
			->from('#__schedule_members');

		foreach ($db->setQuery($query)->loadObjectList() as $member)
		{
			// If already encoded, ignore this record.
			if (strpos($member->password, '$2y$') === 0)
			{
				continue;
			}

			// Hash it
			$member->password = \JUserHelper::hashPassword($member->password);

			// Restore back to DB
			$db->updateObject('#__schedule_members', $member, 'id');

			// Print result
			$this->out('Updated ID :' . $member->id . ' Name: ' . $member->name);
		}

		return true;
	}
}
