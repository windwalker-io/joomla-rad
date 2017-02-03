<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Batch;

use Windwalker\Bootstrap\Message;
use Windwalker\Controller\Admin\AbstractListController;

/**
 * Batch controller.
 *
 * @since 2.0
 */
abstract class AbstractBatchController extends AbstractListController
{
	/**
	 * Batch fields.
	 *
	 * @var array()
	 */
	protected $batch = array();

	/**
	 * Is batch done?.
	 *
	 * @var boolean
	 */
	protected $done = false;

	/**
	 * The name of category foreign key.
	 *
	 * @var string
	 */
	protected $categoryKey = 'catid';

	/**
	 * Prepare execute hook.
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$this->batch = $this->input->get('batch', array(), 'array');

		unset($this->batch['task']);

		// Sanitize data.
		foreach ($this->batch as $key => &$value)
		{
			if ($value == '')
			{
				unset($this->batch[$key]);
			}
		}
	}

	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$db = $this->model->getDb();

		try
		{
			$db->transactionStart();

			$this->preBatchHook();

			$result = $this->doBatch();

			$result = $this->postBatchHook($result);
		}
		catch (\Exception $e)
		{
			$db->transactionRollback();

			$this->setRedirect($this->getFailRedirect(), \JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $e->getMessage()), Message::ERROR_RED);

			return false;
		}

		$db->transactionCommit();

		return $result;
	}

	/**
	 * Method to run batch.
	 *
	 * @throws \Exception
	 * @return array
	 */
	protected function doBatch()
	{
		if (!count($this->cid))
		{
			throw new \Exception(\JText::_('JGLOBAL_NO_ITEM_SELECTED'));
		}

		// Category Access
		if (in_array($this->categoryKey, $this->batch) && !$this->allowCategoryAdd($this->batch, $this->categoryKey))
		{
			throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
		}

		$pks = array_unique($this->cid);

		$result = array();

		foreach ($pks as $pk)
		{
			if (!$pk)
			{
				continue;
			}

			$data = $this->batch;

			// Start Batch Process
			$result[] = $this->save($pk, $data);
		}

		return $result;
	}

	/**
	 * Method to save item.
	 *
	 * @param int   $pk   The primary key value.
	 * @param array $data The item data.
	 *
	 * @return mixed
	 */
	abstract protected function save($pk, $data);

	/**
	 * Pose execute hook.
	 *
	 * @param   mixed  $result  Executed return value.
	 *
	 * @return  mixed
	 */
	protected function postExecute($result = null)
	{
	    if ($result === false)
        {
            return false;
        }

		if (!is_array($result))
		{
			$result = array($result);
		}

		if (!in_array(true, $result, true))
		{
			$this->setRedirect($this->getFailRedirect(), \JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), Message::WARNING_YELLOW);

			return false;
		}

		$this->setRedirect($this->getSuccessRedirect(), \JText::_('JLIB_APPLICATION_SUCCESS_BATCH'), Message::MESSAGE_GREEN);

		return true;
	}

	/**
	 * Prepare batch hook.
	 *
	 * @return  void
	 */
	protected function preBatchHook()
	{
	}

	/**
	 * Post batch hook.
	 *
	 * @param array $result Batch result.
	 *
	 * @return mixed
	 */
	protected function postBatchHook($result)
	{
		return $result;
	}
}
