<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Model\Profile;

use Windwalker\Sqlsync\Factory;

/**
 * Class ProfilesModel
 */
class ProfilesModel extends \JModelBase
{
	/**
	 * getItems
	 *
	 * @return array
	 */
	public function getItems()
	{
		$config = Factory::getConfig();

		$current = $config->get('profile', 'default');

		$profiles = new \FilesystemIterator(SQLSYNC_PROFILE, \FilesystemIterator::SKIP_DOTS);

		$items = array();

		foreach ($profiles as $profile)
		{
			if ($profile->isFile())
			{
				continue;
			}

			$item = new \Stdclass;

			$item->title = $profile->getBasename();
			$item->is_current = ($current == $item->title);
			$item->current_version = '';

			$items[] = $item;
		}

		return $items;
	}

	/**
	 * getList
	 *
	 * @return array
	 */
	public function getList()
	{
		$profiles = new \FilesystemIterator(SQLSYNC_PROFILE, \FilesystemIterator::SKIP_DOTS);

		$items = array();

		foreach ($profiles as $profile)
		{
			if ($profile->isFile())
			{
				continue;
			}

			$items[] = $profile->getBasename();
		}

		return $items;
	}
}
