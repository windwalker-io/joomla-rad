<?php
/**
 * Part of sabrina project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Windwalker\Controller;

use Windwalker\Controller\Admin\AbstractItemController;

/**
 * The ReloadController class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ReloadController extends AbstractItemController
{
	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		// Check for request forgeries.
		$this->checkToken('post', false);

		$app     = $this->app;
		$model   = $this->getModel();
		$data    = $this->input->post->get('jform', array(), 'array');

		// Determine the name of the primary key for the data.
		if (empty($this->key))
		{
			$this->key = $model->getTable()->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($this->urlVar))
		{
			$this->urlVar = $this->key;
		}

		$recordId = $this->input->getInt($this->urlVar);

		if (!$this->allowEdit($data, $this->key))
		{
			$this->setRedirect($this->getRedirectItemUrl($recordId, $this->urlVar));
			$this->redirect();
		}

		// Populate the row id from the session.
		$data[$this->key] = $recordId;

		// The redirect url
		$redirectUrl = $this->getRedirectItemUrl($recordId, $this->urlVar);

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			$this->setRedirect($redirectUrl);
			$this->redirect();
		}

		// Save the data in the session.
		$app->setUserState($this->context . '.data', $form->filter($data));

		$this->setRedirect($redirectUrl);

		return true;
	}
}
