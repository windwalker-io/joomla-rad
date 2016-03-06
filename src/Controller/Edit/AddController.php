<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Edit;

use Windwalker\Bootstrap\Message;
use Windwalker\Controller\Admin\AbstractItemController;

/**
 * Add Controller.
 *
 * @since 2.0
 */
class AddController extends AbstractItemController
{
	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$context = $this->context;

		// Access check.
		if (!$this->allowAdd())
		{
			// Set the internal error and also the redirect error.
			$this->setRedirect($this->getFailRedirect(), \JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), Message::ERROR_RED);

			return false;
		}

		// Clear the record edit information from the session.
		$this->app->setUserState($context . '.data', null);

		// Redirect to the edit screen.
		$this->setRedirect($this->getSuccessRedirect());

		return true;
	}

	/**
	 * Set redirect URL for action success.
	 *
	 * @return  string  Redirect URL.
	 */
	public function getSuccessRedirect()
	{
		$this->input->set('layout', 'edit');

		return parent::getSuccessRedirect();
	}
}
