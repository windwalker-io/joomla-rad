<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller\Batch;

use Joomla\CMS\Form\Form;
use Windwalker\Bootstrap\Message;
use Windwalker\Controller\Admin\AbstractListController;
use Windwalker\Model\Exception\ValidateFailException;

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
	 * Property emptyMark.
	 *
	 * @var  string
	 */
	protected $emptyMark = '__EMPTY__';

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

		$form = $this->getForm();

		// Sanitize data.
		foreach ($this->batch as $key => &$value)
		{
			if ($value === '')
			{
				unset($this->batch[$key]);
			}
			elseif ($value === $this->emptyMark)
			{
				$value = '';
			}
			elseif ($value === '\\' . $this->emptyMark)
			{
				$value = $this->emptyMark;
			}
			
			// Fix for user field
			$field = $form->getField($key, 'batch');

			if ($field->type === 'User' && (int) $value === 0)
			{
				unset($value);
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

			if (JDEBUG)
			{
				throw $e;
			}

			$this->setRedirect($this->getFailRedirect(), \Joomla\CMS\Language\Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $e->getMessage()), Message::ERROR_RED);

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
			throw new \Exception(\Joomla\CMS\Language\Text::_('JGLOBAL_NO_ITEM_SELECTED'));
		}

		// Category Access
		if (in_array($this->categoryKey, $this->batch) && !$this->allowCategoryAdd($this->batch, $this->categoryKey))
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
		}

		$pks = array_unique($this->cid);

		$result = array();

		$data = $this->batch;

		$this->validate($data);
		$data = $this->filter($data);

		foreach ($pks as $pk)
		{
			if (!$pk)
			{
				continue;
			}

			// Start Batch Process
			$result[] = $this->save($pk, $data);
		}

		return $result;
	}

	/**
	 * validate
	 *
	 * @param array $data
	 *
	 * @return  void
	 *
	 * @throws ValidateFailException
	 */
	protected function validate($data)
	{
		$form = $this->getForm();

		if (!$form->validate(array('batch' => $data)))
		{
			throw new ValidateFailException($form->getErrors());
		}
	}

	/**
	 * filter
	 *
	 * @param array $data
	 *
	 * @return  array
	 */
	protected function filter($data)
	{
		$form = $this->getForm();

		$data = $form->filter(array('batch' => $data));

		return isset($data['batch']) ? $data['batch'] : array();
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
			$this->setRedirect($this->getFailRedirect(), \Joomla\CMS\Language\Text::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), Message::WARNING_YELLOW);

			return false;
		}

		$this->setRedirect($this->getSuccessRedirect(), \Joomla\CMS\Language\Text::_('JLIB_APPLICATION_SUCCESS_BATCH'), Message::MESSAGE_GREEN);

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

	/**
	 * getForm
	 *
	 * @return  Form
	 */
	public function getForm()
	{
		return $this->getModel($this->viewList)->getBatchForm();
	}
}
