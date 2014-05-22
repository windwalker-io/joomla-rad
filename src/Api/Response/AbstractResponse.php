<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Response;

/**
 * Class AbstractResponse
 *
 * @since 2.0
 */
abstract class AbstractResponse
{
	/**
	 * Response content as json.
	 *
	 * Using JResponseJson to wrap our content. It contains some information like success or messages.
	 *
	 * @param   mixed   $response       The Response data
	 * @param   string  $message        The main response message
	 * @param   boolean $error          True, if the success flag shall be set to false, defaults to false
	 * @param   boolean $ignoreMessages True, if the message queue shouldn't be included, defaults to false
	 *
	 * @throws \LogicException
	 * @return  string Formatted json response.
	 */
	public static function response($response = null, $message = null, $error = false, $ignoreMessages = false)
	{
		throw new \LogicException('Please override this method.');
	}

	/**
	 * The json error handler.
	 *
	 * @param integer $errno      The level of the error raised, as an integer.
	 * @param string  $errstr     The error message, as a string.
	 * @param string  $errfile    The filename that the error was raised in, as a string.
	 * @param integer $errline    The line number the error was raised at, as an integer.
	 * @param mixed   $errcontext An array that points to the active symbol table at the point the error occurred.
	 *                            In other words, errcontext will contain an array of every variable that existed
	 *                            in the scope the error was triggered in. User error handler must not modify error context.
	 *
	 * @throws \ErrorException
	 * @return  void
	 */
	public static function error($errno ,$errstr ,$errfile, $errline ,$errcontext)
	{
		$content = sprintf('%s. File: %s (line: %s)', $errstr, $errfile, $errno);

		throw new \ErrorException($content, $errno, 1, $errfile, $errline);
	}

	/**
	 * The exception handler.
	 *
	 * @param \Exception $exception The exception object.
	 *
	 * @return  void
	 */
	public static function exception(\Exception $exception)
	{
		try
		{
			$response = static::response($exception);
		}
		catch (\Exception $e)
		{
			$msg = "Infinity loop in exception handler. \n\nException:\n" . $e;

			exit($msg);
		}

		$response->code = $exception->getCode();

		if (JDEBUG)
		{
			$response->backtrace = $exception->getTrace();
		}

		$app = \JFactory::getApplication();
		$doc = \JDocument::getInstance('json');

		$app->setBody($doc->setBuffer($response)->render());

		$app->setHeader('Content-Type', $doc->getMimeEncoding() . '; charset=' . $doc->getCharset());

		echo $app->toString();

		die;
	}

	/**
	 * registerErrorHandler
	 *
	 * @return  void
	 */
	public static function registerErrorHandler()
	{
		restore_error_handler();
		restore_exception_handler();

		set_error_handler(array(get_called_class(), 'error'));
		set_exception_handler(array(get_called_class(), 'exception'));
	}
}
