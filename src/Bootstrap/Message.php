<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Bootstrap;

/**
 * The Message class.
 *
 * @since  {DEPLOY_VERSION}
 */
abstract class Message
{
	/**
	 * Green success.
	 *
	 * @const  string
	 */
	const MESSAGE_GREEN = 'message';

	/**
	 * Blue info.
	 *
	 * @const  string
	 */
	const NOTICE_BLUE = 'notice';

	/**
	 * Yellow warning.
	 *
	 * @const  string
	 */
	const WARNING_YELLOW = 'warning';

	/**
	 * Red danger.
	 *
	 * @const  string
	 */
	const ERROR_RED = 'error';
}
