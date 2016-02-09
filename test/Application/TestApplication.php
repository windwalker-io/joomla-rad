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
use Windwalker\Test\Joomla\MockSession;

/**
 * Class ApplicationCms
 */
class TestApplication extends \JApplicationCms
{
	/**
	 * Property userState.
	 *
	 * @var  \JRegistry
	 */
	public $userState;

	/**
	 * Property messages.
	 *
	 * @var  array
	 */
	public $messages = array();

	/**
	 * Property isAdmin.
	 *
	 * @var  bool
	 */
	public $isAdmin = true;

	/**
	 * Constructor
	 */
	public function __construct(\JInput $input = null, Registry $config = null, \JApplicationWebClient $client = null)
	{
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
	 * clearUserState
	 *
	 * @return  void
	 */
	public function clearUserState()
	{
		$this->userState = new \JRegistry;
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
		$this->session = new MockSession;

		return $this;
	}

	/**
	 * enqueueMessage
	 *
	 * @param string $msg
	 * @param string $type
	 *
	 * @return  void
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		// Don't add empty messages.
		if (!strlen($msg))
		{
			return;
		}

		// Enqueue the message.
		$this->messages[] = array('message' => $msg, 'type' => strtolower($type));
	}

	/**
	 * getMessageQueue
	 *
	 * @return  array
	 */
	public function getMessageQueue()
	{
		return $this->messages;
	}

	/**
	 * clearMessageQueue
	 *
	 * @return  void
	 */
	public function clearMessageQueue()
	{
		$this->messages = array();
	}

	/**
	 * isAdmin
	 *
	 * @return  bool
	 */
	public function isAdmin()
	{
		return $this->isAdmin;
	}

	/**
	 * getParams
	 *
	 * @param string $option
	 *
	 * @return  \JRegistry
	 */
	public function getParams($option = null)
	{
		return new \JRegistry(array(
			'foo' => 'bar',
			'bar' => 'foo',
			'foobar' => 123
		));
	}
}
