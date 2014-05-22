<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Response;

use Windwalker\Api\Buffer\JsonBuffer;

/**
 * Class JsonResponse
 *
 * @since 1.0
 */
class JsonResponse extends AbstractResponse
{
	/**
	 * Response content as json.
	 *
	 * Using JResponseJson to wrap our content. It contains some information like success or messages.
	 *
	 * @param   mixed    $response        The Response data
	 * @param   string   $message         The main response message
	 * @param   boolean  $error           True, if the success flag shall be set to false, defaults to false
	 * @param   boolean  $ignoreMessages  True, if the message queue shouldn't be included, defaults to false
	 *
	 * @return  string Formatted json response.
	 */
	public static function response($response = null, $message = null, $error = false, $ignoreMessages = false)
	{
		return new JsonBuffer($response, $message, $error, $ignoreMessages);
	}
}
