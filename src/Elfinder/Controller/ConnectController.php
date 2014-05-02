<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Elfinder\Controller;

use Windwalker\Controller\Controller;
use Windwalker\Elfinder\View\ConnectView;

/**
 * ElFinder Connnect Controller
 *
 * @since 2.0
 */
class ConnectController extends Controller
{
	/**
	 * Method to run this controller.
	 *
	 * @throws \UnexpectedValueException
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$view = new ConnectView(null, $this->container);

		if (!($view instanceof \JView))
		{
			throw new \UnexpectedValueException(sprintf('Elfinder view: %s not found.', 'ConnectView'));
		}

		$view->setConfig(array());

		return $view->render();
	}
}
