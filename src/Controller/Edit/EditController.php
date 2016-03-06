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
 * Edit Controller
 *
 * @since 2.0
 */
class EditController extends AbstractItemController
{
	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$cid = $this->input->post->get('cid', array(), 'array');

		// Get the previous record id (if any) and the current record id.
		$this->recordId = count($cid) ? $cid[0] : $this->recordId;

		// Access check.
		if (!$this->allowEdit(array($this->key => $this->recordId), $this->key))
		{
			// Set the internal error and also the redirect error.
			$this->setRedirect($this->getFailRedirect(), \JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), Message::ERROR_RED);

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		$this->fetch($this->prefix, strtolower($this->viewList) . '.check.checkout', array('cid' => array($this->recordId)));

		// Check-out succeeded, push the new record id into the session.
		$this->holdEditId($this->context, $this->recordId);

		$this->app->setUserState($this->context . '.data', null);

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
