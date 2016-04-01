<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Model\Profile;

use Joomla\Registry\Registry;
use Windwalker\Sqlsync\Factory;

/**
 * Class ProfileModel
 */
class ProfileModel extends \JModelBase
{
	/**
	 * add
	 *
	 * @param $name
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function add($name)
	{
		$listModel = new ProfilesModel;

		$profiles = $listModel->getList();

		if (in_array($name, $profiles))
		{
			throw new \Exception(sprintf('Profile "%s" exists.', $name));
		}

		$profilePath = SQLSYNC_PROFILE . '/' . $name;

		if (!\JFolder::create($profilePath))
		{
			throw new \Exception(sprintf('Create profile "%s" fail.', $name));
		}

		\JFile::copy(SQLSYNC_LIB . '/Resource/track.yml', $profilePath . '/track.yml');

		return true;
	}

	/**
	 * remove
	 *
	 * @param $name
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function remove($name)
	{
		$listModel = new ProfilesModel;

		$profiles = $listModel->getList();

		if (!in_array($name, $profiles))
		{
			throw new \Exception(sprintf('Profile "%s" not exists.', $name));
		}

		jimport('joomla.filesystem.folder');

		if (!\JFolder::delete(SQLSYNC_PROFILE . '/' . $name))
		{
			throw new \Exception(sprintf('Remove profile "%s" fail.', $name));
		}

		return true;
	}

	/**
	 * checkout
	 *
	 * @param $profile
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function checkout($profile)
	{
		$listModel = new ProfilesModel;

		$profiles = $listModel->getList();

		if (!in_array($profile, $profiles))
		{
			throw new \Exception(sprintf('Profile "%s" not exists.', $name));
		}

		$profileConfig = new Registry;

		$file = JPATH_ROOT . '/tmp/sqlsync/config.yml';

		$profileConfig->loadFile($file);

		$profileConfig->set('profile', $profile);

		$content = $profileConfig->toString('yaml');

		if (!\JFile::write($file, $content))
		{
			throw new \Exception('Writing profile config fail.');
		}

		return true;
	}
}
