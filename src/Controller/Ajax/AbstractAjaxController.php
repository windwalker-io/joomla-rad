<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Ajax;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;
use Windwalker\Controller\Controller;

/**
 * The AbstractAjaxController class.
 *
 * @since  2.1.12
 */
abstract class AbstractAjaxController extends Controller
{
	/**
	 * Property contentType.
	 *
	 * @var  string
	 */
	protected $contentType = 'application/json';

	/**
	 * Property format.
	 *
	 * @var  string
	 */
	protected $format = 'json';

	/**
	 * Property message.
	 *
	 * @var string
	 */
	protected $successMessage;

	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		try
		{
			$this->checkToken();

			$data = $this->doAjax();

			if ($this->format === 'json')
			{
				$buffer = new JsonResponse($data, $this->successMessage);
			}
			else
			{
				$buffer = $data;
			}
		}
		catch (\Exception $e)
		{
			$msg = null;

			if (JDEBUG)
			{
				$msg = array(
					'exception' => get_class($e),
					'file'      => $e->getFile(),
					'line'      => $e->getLine(),
					'trace'     => explode("\n", $e->getTraceAsString())
				);
			}

			$buffer = new JsonResponse($msg, $e->getMessage(), true);

			header('HTTP/1.1 ' . $e->getCode() . ' ' . str_replace('%20', ' ', rawurlencode($e->getMessage())));
		}

		header('Content-Type: ' . $this->contentType);

		echo $buffer;
		die;
	}

	/**
	 * Execute ajax.
	 *
	 * @return  mixed
	 */
	abstract protected function doAjax();

	/**
	 * checkToken
	 *
	 * @param string $method
	 * @param bool   $redirect
	 *
	 * @return bool
	 * @throws \RuntimeException
	 */
	protected function checkToken($method = 'post', $redirect = true)
	{
		if (!Session::checkToken($method))
		{
			throw new \RuntimeException(Text::_('JINVALID_TOKEN_NOTICE'), 400);
		}

		return true;
	}

	/**
	 * Use JSON response.
	 *
	 * @return  static
	 */
	public function useJson()
	{
		$this->setContentType('application/json')
			->setFormat('json');

		return $this;
	}

	/**
	 * Use HTML response.
	 *
	 * @return  static
	 */
	public function useHtml()
	{
		$this->setContentType('text/html')
			->setFormat('html');

		return $this;
	}

	/**
	 * Method to get property ContentType
	 *
	 * @return  string
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * Method to set property contentType
	 *
	 * @param   string $contentType
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setContentType($contentType)
	{
		$this->contentType = $contentType;

		return $this;
	}

	/**
	 * Method to get property Format
	 *
	 * @return  string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * Method to set property format
	 *
	 * @param   string $format
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setFormat($format)
	{
		$this->format = $format;

		return $this;
	}

	/**
	 * Method to get property SuccessMessage
	 *
	 * @return  string
	 */
	public function getSuccessMessage()
	{
		return $this->successMessage;
	}

	/**
	 * Method to set property successMessage
	 *
	 * @param   string $successMessage
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setSuccessMessage($successMessage)
	{
		$this->successMessage = $successMessage;

		return $this;
	}
}
