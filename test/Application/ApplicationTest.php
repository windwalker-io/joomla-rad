<?php
/**
 * Part of windwalker-joomla-rad-test project.
 *
 * @copyright  Copyright (C) 2011 - 2015 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Application;

use JApplicationCms;
use Joomla\Registry\Registry;

/**
 * Class ApplicationCms
 */
class ApplicationTest extends \JApplicationCms
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
	public function __construct(\JInput $input = null, Registry $config = null, \JApplicationWebClient $client = null)
	{
		$_SERVER['HTTP_HOST'] = 'rad.windwalker.io';

		$config = $config ? : new Registry;

		$config->set('session', false);

		parent::__construct($input, $config, $client);

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

	/**
	 * Allows the application to load a custom or default session.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a session,
	 * if required, based on more specific needs.
	 *
	 * @param   \JSession  $session  An optional session object. If omitted, the session is created.
	 *
	 * @return  \JApplicationCms  This method is chainable.
	 *
	 * @since   3.2
	 */
	public function loadSession(\JSession $session = null)
	{
		// Set the session object.
		$this->session = $session;

		return $this;
	}
}
