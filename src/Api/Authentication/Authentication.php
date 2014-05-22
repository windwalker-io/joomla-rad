<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Authentication;

/**
 * Class Authentication
 *
 * @since 1.0
 */
abstract class Authentication
{
	/**
	 * When API access, the client maybe not have session storage.
	 * But Joomla will store session in DB if user has logged in.
	 *
	 * So we get session key from request, then we use this key to find session data in DB, if found, means user has logged in.
	 * We can use get this session data and restore into php session that Joomla will know this user has logged in.
	 *
	 * @param string $sessionKey
	 *
	 * @throws \Exception
	 *
	 * @return  boolean
	 */
	public static function authenticate($sessionKey)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get session key from db storage
		$session = \JFactory::getSession();

		$query->select('userid')
			->from('#__session')
			->where($query->format("%n = %q", 'session_id', $sessionKey));

		$uid = $db->setQuery($query, 0, 1)->loadResult();

		/*
		 * If user has logged in, set it in php session.
		 * Then we use JFactory::getUser(), the JFactory will find user from session prior.
		 */
		if ($uid)
		{
			$user = \JFactory::getUser($uid);

			$session->set('user', $user);
		}

		// Is login?
		$user = \JFactory::getUser();

		if ($user->guest)
		{
			return false;
		}

		return true;
	}
}
