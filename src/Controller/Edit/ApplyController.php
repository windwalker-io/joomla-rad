<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Edit;

/**
 * Apply Controller.
 *
 * @since 2.0
 */
class ApplyController extends SaveController
{
	/**
	 * Pose execute hook.
	 *
	 * @param   mixed  $return  Executed return value.
	 *
	 * @return  mixed
	 */
	protected function postExecute($return = null)
	{
		// Set the record data in the session.
		$this->recordId = $this->model->getState()->get($this->getName() . '.id');
		$this->holdEditId($this->context, $this->recordId);

		// If save success, clean session.
		if ($return)
		{
			$this->app->setUserState($this->context . '.data', null);
		}

		// Redirect back to the edit screen.
		$this->setRedirect($this->getSuccessRedirect());

		return $return;
	}

	/**
	 * Set redirect URL for action success.
	 *
	 * @return  string  Redirect URL.
	 */
	public function getSuccessRedirect()
	{
		return \JRoute::_($this->getRedirectItemUrl($this->recordId, $this->urlVar), false);
	}
}
