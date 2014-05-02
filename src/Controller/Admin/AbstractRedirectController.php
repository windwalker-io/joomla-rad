<?php

namespace Windwalker\Controller\Admin;

use Windwalker\Controller\Controller;
use Windwalker\Helper\UriHelper;

/**
 * Class AbstractRedirectController
 *
 * @since 2.0
 */
abstract class AbstractRedirectController extends Controller
{
	/**
	 * Allowed params in url.
	 *
	 * @var array
	 */
	protected $allowUrlParams = array(
		'tmpl',
		'layout',
		'return'
	);

	/**
	 * Are we allow return?
	 *
	 * @var  boolean
	 */
	protected $allowReturn = false;

	/**
	 * View item name.
	 *
	 * @var string
	 */
	protected $viewItem = null;

	/**
	 * View list name.
	 *
	 * @var string
	 */
	protected $viewList = null;

	/**
	 * Instantiate the controller.
	 *
	 * @param   \JInput          $input   The input object.
	 * @param   \JApplicationCms $app     The application object.
	 * @param   array            $config  The config object.
	 */
	public function __construct(\JInput $input = null, \JApplicationCms $app = null, $config = array())
	{
		parent::__construct($input, $app, $config);

		if (!empty($config['allow_url_params']) && is_array($config['allow_url_params']))
		{
			$this->allowUrlParams = array_merge($this->allowUrlParams, $config['allow_url_params']);
		}

		if (!empty($config['allow_return']))
		{
			$this->allowReturn = $config['allow_return'];
		}
	}

	/**
	 * Prepare execute hook.
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();
	}

	/**
	 * Redirect to item page.
	 *
	 * @param   integer  $recordId  The record item id.
	 * @param   string   $urlVar    The name of the URL variable if different from the primary key.
	 * @param   string   $msg       Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param   string   $type      Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
	 *
	 * @return  void
	 */
	public function redirectToItem($recordId = null, $urlVar = 'id', $msg = null, $type = 'message')
	{
		$this->redirect(\JRoute::_($this->getRedirectItemUrl($recordId, $urlVar), false), $msg, $type);
	}

	/**
	 * Redirect to list page.
	 *
	 * @param   string  $msg   Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param   string  $type  Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
	 *
	 * @return  void
	 */
	public function redirectToList($msg = null, $type = 'message')
	{
		$this->input->set('layout', null);

		$this->redirect(\JRoute::_($this->getRedirectListUrl(), false), $msg, $type);
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @param   string $url  URL to redirect to.
	 * @param   string $msg  Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param   string $type Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
	 *
	 * @return  void
	 */
	public function redirect($url, $msg = null, $type = 'message')
	{
		$this->setMessage($msg, $type);

		if ($this->input->get('hmvc') || !$this->input->get('redirect', true))
		{
			return;
		}

		if ($this->input->get('return') && $this->allowReturn)
		{
			$url = UriHelper::base64('decode', $this->input->get('return'));
		}

		$this->app->redirect($url);
	}

	/**
	 * Get the url of item page redirect.
	 *
	 * @param   integer  $recordId  The record item id.
	 * @param   string   $urlVar    The name of the URL variable if different from the primary key.
	 *
	 * @return  string
	 */
	protected function getRedirectItemUrl($recordId = null, $urlVar = 'id')
	{
		return 'index.php?option=' . $this->option . '&view=' . strtolower($this->viewItem)
			. $this->getRedirectItemAppend($recordId, $urlVar);
	}

	/**
	 * Get the url of list page redirect.
	 *
	 * @return string
	 */
	protected function getRedirectListUrl()
	{
		return 'index.php?option=' . $this->option . '&view=' . strtolower($this->viewList)
			. $this->getRedirectListAppend();
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectItemAppend($recordId = null, $urlVar = 'id')
	{
		$input  = $this->input;
		$params = array();

		array_map(
			function($param) use($input, &$params)
			{
				$value = $input->getString($param);

				if ($value)
				{
					$params[$param] = $value;
				}
			},
			$this->allowUrlParams
		);

		if ($recordId)
		{
			$params[$urlVar] = $recordId;
		}

		return (count($params)) ? '&' . http_build_query($params) : '';
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectListAppend()
	{
		$input  = $this->input;
		$params = array();

		array_map(
			function($param) use($input, &$params)
			{
				$value = $input->getString($param);

				if ($value)
				{
					$params[$param] = $value;
				}
			},
			$this->allowUrlParams
		);

		return (count($params)) ? '&' . http_build_query($params) : '';
	}
}
