<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Buffer;

use Windwalker\Data\Data;

/**
 * Class AbstractBuffer
 *
 * @since 1.0
 */
class AbstractBuffer extends Data
{
	/**
	 * Determines whether the request was successful
	 *
	 * @var  boolean
	 */
	public $success = true;

	/**
	 * The main response message
	 *
	 * @var  string
	 */
	public $message = null;

	/**
	 * Array of messages gathered in the JApplication object
	 *
	 * @var  array
	 */
	public $messages = null;

	/**
	 * The response data
	 *
	 * @var  mixed
	 */
	public $data = null;

	/**
	 * Constructor
	 *
	 * @param mixed  $data
	 * @param bool   $success
	 * @param string $message
	 * @param array  $messages
	 */
	public function __construct($data = null, $success = true, $message = null, $messages = array())
	{
		$this->data = $data;
		$this->success = $success;
		$this->message = $message;
		$this->messages = $messages;
	}
}
