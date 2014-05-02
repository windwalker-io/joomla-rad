<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller\Admin;

use Windwalker\Model\CrudModel;
use Windwalker\Table\Table;

/**
 * A controller to handle admin operation.
 *
 * @since 1.0
 */
abstract class AbstractAdminController extends AbstractRedirectController
{
	/**
	 * The context for storing internal data, e.g. record.
	 *
	 * @var  string
	 */
	protected $context = null;

	/**
	 * The user object.
	 *
	 * @var \JUser
	 */
	protected $user = null;

	/**
	 * Text prefix for translate.
	 *
	 * @var string
	 */
	protected $textPrefix = null;

	/**
	 * The name of the primary key of the URL variable.
	 *
	 * @var string
	 */
	protected $key = null;

	/**
	 * The name of the URL variable if different from the primary key.
	 *
	 * @var string
	 */
	protected $urlVar = null;

	/**
	 * Table object.
	 *
	 * @var Table
	 */
	protected $table = null;

	/**
	 * Model object, need CrudModel in this controller.
	 *
	 * @var CrudModel
	 */
	protected $model = null;

	/**
	 * Language object.
	 *
	 * @var \JLanguage
	 */
	protected $lang = null;

	/**
	 * Instantiate the controller.
	 *
	 * @param   \JInput          $input  The input object.
	 * @param   \JApplicationCms $app    The application object.
	 * @param   array            $config Additional config.
	 *
	 * @throws  \Exception
	 */
	public function __construct(\JInput $input = null, \JApplicationCms $app = null, $config = array())
	{
		parent::__construct($input, $app, $config);

		$this->context    = $this->option . '.' . $this->task;
		$this->textPrefix = strtoupper($this->option);
	}

	/**
	 * Prepare execute hook.
	 *
	 * @throws \UnexpectedValueException
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$this->user  = $this->container->get('user');
		$this->lang  = $this->container->get('language');
		$this->model = $this->getModel($this->viewItem);
		$this->table = $this->model->getTable($this->viewItem, $this->prefix . 'Table');

		// Determine model
		if (!($this->model instanceof CrudModel))
		{
			throw new \UnexpectedValueException(sprintf('%s model need extend to CrudModel', $this->name));
		}

		// Determine the name of the primary key for the data.
		if (empty($this->key))
		{
			$this->key = $this->table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($this->urlVar))
		{
			$this->urlVar = $this->key;
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array())
	{
		return (
			$this->user->authorise('core.create', $this->option)
			|| count($this->user->getAuthorisedCategories($this->option, 'core.create'))
		);
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowSave($data, $key = 'id')
	{
		$recordId = isset($data[$key]) ? $data[$key] : '0';

		if ($recordId)
		{
			return $this->allowEdit($data, $key);
		}
		else
		{
			return $this->allowAdd($data);
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return $this->user->authorise('core.edit', $this->option);
	}

	/**
	 * Check update access.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowUpdateState($data = array(), $key = 'id')
	{
		return $this->user->authorise('core.edit.state', $this->option);
	}

	/**
	 * Method to check delete access.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowDelete($data = array(), $key = 'id')
	{
		return $this->user->authorise('core.edit', $this->option);
	}

	/**
	 * If category need authorize, we can write in this method.
	 *
	 * @param   array   $data  Category record.
	 * @param   string  $key   Preimary key name.
	 *
	 * @return  boolean Can edit or not.
	 */
	public function allowCategoryAdd($data, $key = 'catid')
	{
		return $this->user->authorise('core.create', $this->option . '.category.' . $data[$key]);
	}
}
