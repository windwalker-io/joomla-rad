<?php
/**
 * Part of windwalker-joomla-rad-test project.
 *
 * @copyright  Copyright (C) 2011 - 2015 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Mock;

/**
 * Class ApplicationCms
 */
class ApplicationCms extends \JApplicationCms
{
	/**
	 * Property userState.
	 *
	 * @var  \JRegistry
	 */
	public $userState;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->userState = new \JRegistry;
	}

	/**
	 * Gets a user state.
	 *
	 * @param   string  $key      The path of the state.
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  The user state or null.
	 */
	public function getUserState($key, $default = null)
	{
		return $this->userState->get($key, $default);
	}

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param   string  $key    The path of the state.
	 * @param   string  $value  The value of the variable.
	 *
	 * @return  mixed  The previous state, if one existed.
	 */
	public function setUserState($key, $value)
	{
		return $this->userState->set($key, $value);
	}
}
