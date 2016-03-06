<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Joomla;

use Joomla\Registry\Registry;

/**
 * The MockSession class.
 * 
 * @since  2.1
 */
class MockSession extends \JSession
{
	/**
	 * Property data.
	 *
	 * @var  Registry
	 */
	protected $data = null;

	/**
	 * Property sessionName.
	 *
	 * @var  string
	 */
	protected $sessionName = 'WINDWALKER_SESSION_ID';

	/**
	 * Property sessionId.
	 *
	 * @var  string
	 */
	protected $sessionId = '1qaz2wsx3edc';

	/**
	 * Constructor
	 *
	 * @since   11.1
	 *
	 * @param array $data
	 */
	public function __construct($data = array())
	{
		// Will load Registry to $this->data
		$this->start();

		$this->data->loadArray((array) $data);
	}

	/**
	 * Set data into the session store.
	 *
	 * @param   string  $name       Name of a variable.
	 * @param   mixed   $value      Value of a variable.
	 * @param   string  $namespace  Namespace to use, default to 'default'.
	 *
	 * @return  mixed  Old value of a variable.
	 *
	 * @since   11.1
	 */
	public function set($name, $value = null, $namespace = 'default')
	{
		return $this->data->set($namespace . '.' . $name, $value);
	}

	/**
	 * Get data from the session store
	 *
	 * @param   string  $name       Name of a variable
	 * @param   mixed   $default    Default value of a variable if not set
	 * @param   string  $namespace  Namespace to use, default to 'default'
	 *
	 * @return  mixed  Value of a variable
	 *
	 * @since   11.1
	 */
	public function get($name, $default = null, $namespace = 'default')
	{
		return $this->data->get($namespace . '.' . $name, $default);
	}

	/**
	 * Returns the global Session object, only creating it if it doesn't already exist.
	 *
	 * @param   string                    $store             The type of storage for the session.
	 * @param   array                     $options           An array of configuration options.
	 * @param   \JSessionHandlerInterface  $handlerInterface  The session handler
	 *
	 * @return  static  The Session object.
	 *
	 * @since   11.1
	 */
	public static function getInstance($store, $options, \JSessionHandlerInterface $handlerInterface = null)
	{
		if (!is_object(self::$instance))
		{
			static::$instance = new static($store, $options, $handlerInterface);
		}

		return self::$instance;
	}

	/**
	 * Get current state of session
	 *
	 * @return  string  The session state
	 *
	 * @since   11.1
	 */
	public function getState()
	{
		return $this->_state;
	}

	/**
	 * Get expiration time in minutes
	 *
	 * @return  integer  The session expiration time in minutes
	 *
	 * @since   11.1
	 */
	public function getExpire()
	{
		return $this->_expire;
	}

	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 *
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 *
	 * @param   boolean  $forceNew  If true, force a new token to be created
	 *
	 * @return  string  The session token
	 *
	 * @since   11.1
	 */
	public function getToken($forceNew = false)
	{
		$token = $this->get('session.token');

		// Create a token
		if ($token === null || $forceNew)
		{
			$token = $this->_createToken(12);
			$this->set('session.token', $token);
		}

		return $token;
	}

	/**
	 * Retrieve an external iterator.
	 *
	 * @return  \ArrayIterator  Return an ArrayIterator of $_SESSION.
	 *
	 * @since   12.2
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->data->toArray());
	}

	/**
	 * Checks for a form token in the request.
	 *
	 * Use in conjunction with JHtml::_('form.token') or JSession::getFormToken.
	 *
	 * @param   string  $method  The request method in which to look for the token key.
	 *
	 * @return  boolean  True if found and valid, false otherwise.
	 *
	 * @since   12.1
	 */
	public static function checkToken($method = 'post')
	{
		$token = self::getFormToken();
		$app = \JFactory::getApplication();

		if (!$app->input->$method->get($token, '', 'alnum'))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Get session name
	 *
	 * @return  string  The session name
	 *
	 * @since   11.1
	 */
	public function getName()
	{
		return $this->sessionName;
	}

	/**
	 * Get session id
	 *
	 * @return  string  The session name
	 *
	 * @since   11.1
	 */
	public function getId()
	{
		return $this->sessionId;
	}

	/**
	 * Get the session handlers
	 *
	 * @return  array  An array of available session handlers
	 *
	 * @since   11.1
	 */
	public static function getStores()
	{
		return array();
	}

	/**
	 * Shorthand to check if the session is active
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	public function isActive()
	{
		return true;
	}

	/**
	 * Check whether this session is currently created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function isNew()
	{
		$counter = $this->get('session.counter');

		return (bool) ($counter === 1);
	}

	/**
	 * Check whether data exists in the session store
	 *
	 * @param   string  $name       Name of variable
	 * @param   string  $namespace  Namespace to use, default to 'default'
	 *
	 * @return  boolean  True if the variable exists
	 *
	 * @since   11.1
	 */
	public function has($name, $namespace = 'default')
	{
		return $this->data->exists($namespace . '.' . $name);
	}

	/**
	 * Unset data from the session store
	 *
	 * @param   string  $name       Name of variable
	 * @param   string  $namespace  Namespace to use, default to 'default'
	 *
	 * @return  mixed   The value from session or NULL if not set
	 *
	 * @since   11.1
	 */
	public function clear($name, $namespace = 'default')
	{
		return $this->data->set($namespace . '.' . $name, null);
	}

	/**
	 * Start a session.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function start()
	{
		$this->data = new Registry;
	}

	/**
	 * Start a session.
	 *
	 * Creates a session (or resumes the current one based on the state of the session)
	 *
	 * @return  boolean  true on success
	 *
	 * @since   11.1
	 */
	protected function _start()
	{
		return true;
	}

	/**
	 * Frees all session variables and destroys all data registered to a session
	 *
	 * This method resets the $_SESSION variable and destroys all of the data associated
	 * with the current session in its storage (file or DB). It forces new session to be
	 * started after this method is called. It does not unset the session cookie.
	 *
	 * @return  boolean  True on success
	 *
	 * @see     session_destroy()
	 * @see     session_unset()
	 * @since   11.1
	 */
	public function destroy()
	{
		$this->start();

		return true;
	}

	/**
	 * Restart an expired or locked session.
	 *
	 * @return  boolean  True on success
	 *
	 * @see     JSession::destroy()
	 * @since   11.1
	 */
	public function restart()
	{
		return true;
	}

	/**
	 * Create a new session and copy variables from the old one
	 *
	 * @return  boolean $result true on success
	 *
	 * @since   11.1
	 */
	public function fork()
	{
		return true;
	}

	/**
	 * Writes session data and ends session
	 *
	 * Session data is usually stored after your script terminated without the need
	 * to call JSession::close(), but as session data is locked to prevent concurrent
	 * writes only one script may operate on a session at any time. When using
	 * framesets together with sessions you will experience the frames loading one
	 * by one due to this locking. You can reduce the time needed to load all the
	 * frames by ending the session as soon as all changes to session variables are
	 * done.
	 *
	 * @return  void
	 *
	 * @see     session_write_close()
	 * @since   11.1
	 */
	public function close()
	{
	}

	/**
	 * Set session cookie parameters
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function _setCookieParams()
	{
	}

	/**
	 * Create a token-string
	 *
	 * @param   integer  $length  Length of string
	 *
	 * @return  string  Generated token
	 *
	 * @since   11.1
	 */
	protected function _createToken($length = 32)
	{
		static $chars = '0123456789abcdef';
		$max = strlen($chars) - 1;
		$token = '';
		$name = $this->sessionName;

		for ($i = 0; $i < $length; ++$i)
		{
			$token .= $chars[(rand(0, $max))];
		}

		return md5($token . $name);
	}

	/**
	 * Set counter of session usage
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	protected function _setCounter()
	{
		$counter = $this->get('session.counter', 0);
		++$counter;

		$this->set('session.counter', $counter);

		return true;
	}

	/**
	 * Set the session timers
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	protected function _setTimers()
	{
		return true;
	}

	/**
	 * Set additional session options
	 *
	 * @param   array  $options  List of parameter
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	protected function _setOptions(array $options)
	{
		return true;
	}

	/**
	 * Do some checks for security reason
	 *
	 * @param   boolean  $restart  Reactivate session
	 *
	 * @return  boolean  True on success
	 *
	 * @see     http://shiflett.org/articles/the-truth-about-sessions
	 * @since   11.1
	 */
	protected function _validate($restart = false)
	{
		return true;
	}
}
