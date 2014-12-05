<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller\Edit;

/**
 * Save2Copy Controller
 *
 * @since 2.0
 */
class Save2copyController extends ApplyController
{
	/**
	 * Method to do something before save.
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		// Attempt to check-in the current record.
		$data = array('cid' => array($this->recordId), 'quiet' => true);

		$this->fetch($this->prefix, $this->viewList . '.check.checkin', $data);

		// Reset the ID and then treat the request as for Apply.
		$this->data[$this->key] = 0;
		$this->data['checked_out'] = '';
		$this->data['checked_out_time'] = '';

		if (isset($this->data['title']))
		{
			$this->data['title'] = \JString::increment($this->data['title']);
		}

		if (isset($this->data['alias']))
		{
			$this->data['alias'] = \JString::increment($this->data['alias'], 'dash');
		}
	}

	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$data = array('jform' => $this->data, $this->key => 0);

		$input = new \JInput($data);

		$input->post = $input;

		$result = $this->fetch($this->prefix, $this->name . '.edit.save', $input);

		$this->input->set('layout', 'edit');

		return $result;
	}
}
