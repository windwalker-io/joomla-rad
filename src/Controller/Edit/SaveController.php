<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller\Edit;

use JArrayHelper;
use Windwalker\Controller\Admin\AbstractItemController;
use Windwalker\Model\Exception\ValidateFailException;

/**
 * Save Controller
 *
 * @since 2.0
 */
class SaveController extends AbstractItemController
{
	/**
	 * Language object.
	 *
	 * @var \JLanguage
	 */
	protected $lang = null;

	/**
	 * Are we allow return?
	 *
	 * @var  boolean
	 */
	protected $allowReturn = true;

	/**
	 * Use DB transaction or not.
	 *
	 * @var  boolean
	 */
	protected $useTransaction = false;

	/**
	 * Instantiate the controller.
	 *
	 * @param   \JInput          $input  The input object.
	 * @param   \JApplicationCms $app    The application object.
	 * @param   array            $config The config object.
	 */
	public function __construct(\JInput $input = null, \JApplicationCms $app = null, $config = array())
	{
		parent::__construct($input, $app, $config);

		$this->key    = $this->key ? : JArrayHelper::getValue($config, 'key');
		$this->urlVar = $this->urlVar ? : JArrayHelper::getValue($config, 'urlVar');
	}

	/**
	 * Prepare execute hook.
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
		$this->checkToken();

		parent::prepareExecute();
	}

	/**
	 * Method to run this controller.
	 *
	 * @throws \Exception
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$db = $this->model->getDb();

		$this->useTransaction ? $db->transactionStart(true) : null;

		try
		{
			$this->preSaveHook();

			$validData = $this->doSave();

			// Invoke the postSave method to allow for the child class to access the model.
			$this->postSaveHook($this->model, $validData);

			// Set success message
			$this->setMessage(
				\JText::_(
					($this->lang->hasKey(strtoupper($this->option) . ($this->recordId == 0 && $this->app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
						? strtoupper($this->option)
						: 'JLIB_APPLICATION') . ($this->recordId == 0 && $this->app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
				),
				'message'
			);
		}

		// Valid fail here
		catch (ValidateFailException $e)
		{
			$this->useTransaction ? $db->transactionRollback(true) : null;

			$errors = $e->getErrors();

			foreach ($errors as $error)
			{
				if ($error instanceof \Exception)
				{
					$this->setMessage($error->getMessage(), 'warning');
				}
				else
				{
					$this->setMessage($error, 'warning');
				}
			}

			// Save the data in the session.
			$this->app->setUserState($this->context . '.data', $this->data);

			// Redirect back to the edit screen.
			$this->redirectToItem($this->recordId, $this->urlVar);

			return false;
		}

		// Other error here
		catch (\Exception $e)
		{
			$this->useTransaction ? $db->transactionRollback(true) : null;

			if (JDEBUG)
			{
				throw $e;
			}

			$this->redirectToItem($this->recordId, $this->urlVar, $e->getMessage(), 'error');

			return false;
		}

		$this->useTransaction ? $db->transactionCommit(true) : null;

		return true;
	}

	/**
	 * Do the save action.
	 *
	 * @throws \Exception
	 * @return array Validated data.
	 */
	protected function doSave()
	{
		$key  = $this->key;

		// Access check.
		if (!$this->allowSave($this->data, $key))
		{
			throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $this->model->getForm($this->data, false);

		// Test whether the data is valid.
		$validData = $this->model->validate($form, $this->data);

		if (!isset($validData['tags']))
		{
			$validData['tags'] = null;
		}

		// Attempt to save the data.
		try
		{
			$this->model->save($validData);
		}
		catch (\Exception $e)
		{
			// Save the data in the session.
			$this->app->setUserState($this->context . '.data', $validData);

			// Redirect back to the edit screen.
			throw new \Exception(\JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $e->getMessage()), 500, $e);
		}

		return $validData;
	}

	/**
	 * Pose execute hook.
	 *
	 * @param   mixed  $return  Executed return value.
	 *
	 * @return  mixed
	 */
	protected function postExecute($return = null)
	{
		$this->input->set('layout', null);

		// Attempt to check-in the current record.
		$data = array('cid' => array($this->recordId), 'quiet' => true);

		$this->fetch($this->prefix, $this->viewList . '.check.checkin', $data);

		// Clear the record id and data from the session.
		$this->releaseEditId($this->context, $this->recordId);

		// If save success, clean session.
		if ($return)
		{
			$this->app->setUserState($this->context . '.data', null);
		}

		$this->redirectToList();

		return $return;
	}

	/**
	 * Get key.
	 *
	 * @return  mixed
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Set key
	 *
	 * @param string $key Key name.
	 *
	 * @return  $this
	 */
	public function setKey($key)
	{
		$this->key = $key;

		return $this;
	}

	/**
	 * Get UrlVar.
	 *
	 * @return  mixed
	 */
	public function getUrlVar()
	{
		return $this->urlVar;
	}

	/**
	 * Set UrlVar.
	 *
	 * @param string $urlVar UrlVar.
	 *
	 * @return  $this
	 */
	public function setUrlVar($urlVar)
	{
		$this->urlVar = $urlVar;

		return $this;
	}

	/**
	 * Method that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   \Windwalker\Model\CrudModel  $model      The data model object.
	 * @param   array                        $validData  The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook($model, $validData)
	{
	}

	/**
	 * Method to do something before save.
	 *
	 * @return void
	 */
	protected function preSaveHook()
	{
	}
}
