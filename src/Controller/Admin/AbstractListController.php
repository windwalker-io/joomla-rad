<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller\Admin;

/**
 * A controller to handle list page operation.
 *
 * @since 2.0
 */
abstract class AbstractListController extends AbstractAdminController
{
	/**
	 * Items id list.
	 *
	 * @var int[]
	 */
	protected $cid;

	/**
	 * Instantiate the controller.
	 *
	 * @param \JInput          $input   The input object.
	 * @param \JApplicationCms $app     The application object.
	 * @param array            $config  Alternative config.
	 */
	public function __construct(\JInput $input = null, \JApplicationCms $app = null, $config = array())
	{
		parent::__construct($input, $app, $config);

		// Guess the item view as the context.
		$this->viewList = $this->viewList ? : \JArrayHelper::getValue($config, 'view_list', $this->getName());

		// Guess the list view as the plural of the item view.
		$this->viewItem = $this->viewItem ? : \JArrayHelper::getValue($config, 'view_item');

		if (empty($this->viewItem))
		{
			$inflector = \JStringInflector::getInstance();

			$this->viewItem = $inflector->toSingular($this->viewList);
		}
	}

	/**
	 * Prepare execute hook.
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
		parent::prepareExecute();

		$this->cid     = $this->input->get('cid', array(), 'array');
		$this->context = $this->option . '.list.' . $this->context;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name     The model name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $config   Configuration array for model. Optional.
	 * @param   boolean $forceNew Force get new model, or we get it from cache.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = null, $prefix = null, $config = array(), $forceNew = false)
	{
		if (!$name)
		{
			$name = $this->viewItem;
		}

		return parent::getModel($name, $prefix, $config);
	}
}
