<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Edit;

use Windwalker\String\StringHelper;

/**
 * Save2Copy Controller
 *
 * @since 2.0
 */
class Save2copyController extends SaveController
{
	/**
	 * Pose execute hook.
	 *
	 * @param   mixed $return Executed return value.
	 *
	 * @return  mixed
	 */
	protected function postExecute($return = null)
	{
		// Run old save process to release edit id.
		parent::postExecute($return);

		// Attempt to check-in the current record.
		$data = array('cid' => array($this->recordId), 'quiet' => true);

		$this->fetch($this->prefix, $this->viewList . '.check.checkin', $data);

		// Reset the ID and then treat the request as for Apply.
		$this->data[$this->key] = 0;
		$this->data['checked_out'] = '';
		$this->data['checked_out_time'] = '';

		if (isset($this->data['title']))
		{
			$this->data['title'] = StringHelper::increment($this->data['title']);
		}

		if (isset($this->data['alias']))
		{
			$this->data['alias'] = StringHelper::increment($this->data['alias'], 'dash');
		}

		// Set new date into session.
		$this->app->setUserState($this->context . '.data', $this->data);

		return $return;
	}

	/**
	 * Set redirect URL for action success.
	 *
	 * @return  string  Redirect URL.
	 */
	public function getSuccessRedirect()
	{
		$this->input->set('layout', 'edit');
		$this->input->set($this->urlVar, null);
		$this->recordId = null;

		return \JRoute::_($this->getRedirectItemUrl($this->recordId, $this->urlVar), false);
	}
}
