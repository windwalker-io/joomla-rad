<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Helper;

use Joomla\Filesystem\Folder;
use Windwalker\Sqlsync\Factory;

/**
 * Class ProfileHelper
 */
abstract class ProfileHelper
{
	/**
	 * getProfile
	 *
	 * @return mixed
	 */
	static public function getProfile()
	{
		$config = Factory::getConfig();

		return $config->get('profile', 'default');
	}

	/**
	 * getPath
	 *
	 * @return string
	 */
	static public function getPath()
	{
		$profile = self::getProfile();

		return SQLSYNC_RESOURCE . '/' . $profile;
	}

	/**
	 * getAllProfiles
	 *
	 * @return  array
	 */
	public static function getAllProfiles()
	{
		return Folder::folders(SQLSYNC_RESOURCE);
	}

	/**
	 * getTmpPath
	 *
	 * @return string
	 */
	static public function getTmpPath()
	{
		$profile = self::getProfile();

		return JPATH_ROOT . '/tmp/sqlsync/' . $profile;
	}
}
