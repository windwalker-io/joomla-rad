<?php

namespace Windwalker\Controller\State;

use Windwalker\Controller\Admin\AbstractListController;

/**
 * Abstract UpdateState Controller
 *
 * @since 2.0
 */
abstract class AbstractUpdateStateController extends AbstractListController
{
	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array();

	/**
	 * Action text for translate.
	 *
	 * @var string
	 */
	protected $actionText = 'STATE_CHANGED';

	/**
	 * Use DB transaction or not.
	 *
	 * @var  boolean
	 */
	protected $useTransaction = false;

	/**
	 * Prepare execute hook.
	 *
	 * @throws \LogicException
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		if (!$this->stateData)
		{
			throw new \LogicException('You have to set state name in controller.');
		}
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
			$this->preUpdateHook();

			$this->doUpdate();

			// Invoke the postSave method to allow for the child class to access the model.
			$this->postUpdateHook($this->model);

			// Set success message
		}
		// Other error here
		catch (\Exception $e)
		{
			$this->useTransaction ? $db->transactionRollback(true) : null;

			if (JDEBUG)
			{
				throw $e;
			}

			$this->redirectToList($e->getMessage(), 'error');

			return false;
		}

		$this->useTransaction ? $db->transactionCommit(true) : null;

		return true;
	}

	/**
	 * Method to do update action.
	 *
	 * @throws \InvalidArgumentException
	 * @return boolean Update success or not.
	 */
	public function doUpdate()
	{
		if (empty($this->cid))
		{
			throw new \InvalidArgumentException(\JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'), 500);
		}

		$pks = $this->cid;

		foreach ($pks as $i => $pk)
		{
			$this->table->reset();

			if ($this->table->load($pk))
			{
				if (!$pk)
				{
					unset($pks[$i]);

					continue;
				}

				if (!$this->allowUpdateState($this->table->getProperties(true)))
				{
					// Prune items that you can't change.
					unset($pks[$i]);

					$this->setMessage(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		if (!$this->model->updateState($pks, $this->stateData))
		{
			return false;
		}

		$errors = $this->model->getState()->get('error.message');

		if (count($errors))
		{
			$this->setMessage(implode('<br />', $errors));
		}

		return true;
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
		// Check in the items.
		$msg = \JText::plural($this->option . '_N_ITEMS_' . $this->actionText, $this->model->getState()->get('success.number'));

		$this->redirectToList($msg);

		return $return;
	}

	/**
	 * Prepare update hook.
	 *
	 * @return void
	 */
	protected function preUpdateHook()
	{
	}

	/**
	 * Pose update hook.
	 *
	 * @param \Windwalker\Model\Model $model
	 *
	 * @return void
	 */
	protected function postUpdateHook($model)
	{
	}
}
