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
 * Class LogoutController
 *
 * @since 2.0
 */
class LogoutController extends Controller
{
	/**
	 * doExecute
	 *
	 * @throws \Exception
	 * @return mixed
	 */
	protected function doExecute()
	{
		$app  = $this->container->get('app');
		$user = $this->container->get('user');

		if (!$user->get('id'))
		{
			throw new \Exception('No user information.');
		}

		$app->logout($user->get('id'));
		$app->enqueueMessage('Logout success');

		$view = new ApiView;

		return $view;
	}
}
