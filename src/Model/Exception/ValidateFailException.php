<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model\Exception;

/**
 * THe Validate Fail Exception.
 *
 * @since 2.0
 */
class ValidateFailException extends \Exception
{
	/**
	 * Errors bag.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Constructor.
	 *
	 * @param array $errors The validate errors.
	 */
	public function __construct(array $errors)
	{
		$this->errors = $errors;

		parent::__construct();
	}

	/**
	 * Get errors.
	 *
	 * @return array Error messages.
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Set errors.
	 *
	 * @param array $errors The error messages.
	 *
	 * @return ValidateFailException Return self to support chaining.
	 */
	public function setErrors(array $errors)
	{
		$this->errors = $errors;

		return $this;
	}
}
