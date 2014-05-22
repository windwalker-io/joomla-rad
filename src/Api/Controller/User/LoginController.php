<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Api\Controller\User;

use Windwalker\Api\View\ApiView;
use Windwalker\Controller\Controller;

/**
 * Class LoginController
 *
 * @since 2.0
 */
class LoginController extends Controller
{
	/**
	 * doExecute
	 *
	 * @throws \Exception
	 * @return mixed
	 */
	protected function doExecute()
	{
		$username = $this->input->getString('username');
		$password = $this->input->getString('password');

		// Execute Login
		$loginResult = $this->app->login(array('username' => $username, 'password' => $password), array('remember' => true));

		if (!$loginResult)
		{
			throw new \Exception('Invaild username or password.');
		}

		$this->app->enqueueMessage('Login success.');

		// Get Session Key
		$session = \JFactory::getSession();

		$view = new ApiView;

		/** @var $data \Joomla\Registry\Registry */
		$data = $view->getData();

		$data['session_key'] = $session->getId();

		return (string) $view;
	}
}
