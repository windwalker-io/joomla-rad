<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View\Html;

use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Model\Model;
use Windwalker\View\Helper\GridHelper;

/**
 * The grid view.
 *
 * @since 2.0
 */
class GridView extends ListHtmlView
{
	/**
	 * The fields mapper.
	 *
	 * @var  array
	 */
	protected $fields = array(
		'pk'               => 'id',
		'title'            => 'title',
		'alias'            => 'alias',
		'checked_out'      => 'checked_out',
		'state'            => 'state',
		'author'           => 'created_by',
		'author_name'      => 'user_name',
		'checked_out_time' => 'checked_out_time',
		'created'          => 'created',
		'language'         => 'language',
		'lang_title'       => 'lang_title'
	);

	/**
	 * The grid config.
	 *
	 * @var  array
	 */
	protected $gridConfig = array();

	/**
	 * Method to instantiate the view.
	 *
	 * @param Model             $model     The model object.
	 * @param Container         $container DI Container.
	 * @param array             $config    View config.
	 * @param \SplPriorityQueue $paths     Paths queue.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array(), \SplPriorityQueue $paths = null)
	{
		parent::__construct($model, $container, $config, $paths);

		if (!empty($config['fields']) && is_array($config['fields']))
		{
			$this->fields = array_merge($config['fields']);
		}

		if (!empty($config['grid']) && is_array($config['grid']))
		{
			$this->gridConfig = array_merge($config['grid']);
		}
	}

	/**
	 * Prepare render hook.
	 *
	 * @return  void
	 */
	protected function prepareRender()
	{
		parent::prepareRender();

		$data             = $this->getData();
		$data->grid       = $this->getGridHelper($this->gridConfig);
		$data->filterForm = $this->get('FilterForm');
		$data->batchForm  = $this->get('BatchForm');

		if ($errors = $data->state->get('errors'))
		{
			$this->flash($errors);
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->addSubmenu();

			$data->sidebar = \JHtmlSidebar::render();

			$this->setTitle();
		}
	}

	/**
	 * Set title of this page.
	 *
	 * @param string $title Page title.
	 * @param string $icons Title icon.
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'stack article')
	{
		if (!$title)
		{
			$title = \JText::_(sprintf('COM_%s_%s_TITLE_LIST', strtoupper($this->prefix), strtoupper($this->getName())));
		}

		parent::setTitle($title, $icons);
	}

	/**
	 * Add the submenu.
	 *
	 * @return  void
	 */
	protected function addSubmenu()
	{
		$helper = ucfirst($this->prefix) . 'Helper';

		if (is_callable(array($helper, 'addSubmenu')))
		{
			$helper::addSubmenu($this->getName());
		}
	}

	/**
	 * Configure the toolbar button set.
	 *
	 * @param   array   $buttonSet Customize button set.
	 * @param   object  $canDo     Access object.
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		$component = $this->container->get('component');
		$canDo     = $canDo ? : $component->getActions($this->viewItem);
		$state     = $this->data->state ? : new Registry;
		$grid      = $this->data->grid;

		$filterState = $state->get('filter', array());

		return array(
			'add' => array(
				'handler'  => 'addNew',
				'args'     => array($this->viewItem . '.edit.add'),
				'access'   => 'core.create',
				'priority' => 1000
			),

			'edit' => array(
				'handler'  => 'editList',
				'args'     => array($this->viewItem . '.edit.edit'),
				'access'   => 'core.edit',
				'priority' => 900
			),

			'duplicate' => array(
				'handler'  => 'duplicate',
				'args'     => array($this->viewList . '.batch.copy'),
				'access'   => 'core.create',
				'priority' => 800
			),

			'publish' => array(
				'handler'  => 'publish',
				'args'     => array($this->viewList . '.state.publish'),
				'access'   => 'core.edit.state',
				'priority' => 700
			),

			'unpublish' => array(
				'handler'  => 'unpublish',
				'args'     => array($this->viewList . '.state.unpublish'),
				'access'   => 'core.create',
				'priority' => 600
			),

			'checkin' => array(
				'handler'  => 'checkin',
				'args'     => array($this->viewList . '.check.checkin'),
				'access'   => 'core.create',
				'priority' => 500
			),

			'delete' => array(
				'handler' => 'deleteList',
				'args'     => array($this->viewList . '.state.delete'),
				'access'  => (
					ArrayHelper::getValue($filterState, $grid->config->get('field.state', 'state'))
					&& $canDo->get('core.delete')
				),
				'priority' => 400
			),

			'trash' => array(
				'handler' => 'trash',
				'args'     => array($this->viewList . '.state.trash'),
				'access'  => (
					!ArrayHelper::getValue($filterState, $grid->config->get('field.state', 'state'))
					&& $canDo->get('core.edit.state')
				),
				'priority' => 300
			),

			'batch' => array(
				'handler'  => 'modal',
				'access'   => 'core.edit',
				'priority' => 200
			),

			'preferences' => array(
				'handler' => 'preferences',
				'access'   => 'core.edit',
				'priority' => 100
			),
		);
	}

	/**
	 * getGridHelper
	 *
	 * @param array $config
	 *
	 * @return  GridHelper
	 */
	public function getGridHelper($config = array())
	{
		$defaultConfig = array(
			'option'    => $this->option,
			'view_name' => $this->getName(),
			'view_item' => $this->viewItem,
			'view_list' => $this->viewList,
			'order_column'   => $this->viewItem . '.ordering',
			'order_table_id' => $this->viewItem . 'List',
			'ignore_access'  => false
		);

		$config['field'] = !empty($config['field']) ? $config['field'] : $this->configureFields();

		$config = with(new Registry($defaultConfig))
			->loadArray($config);

		return new GridHelper($this->data, $config);
	}

	/**
	 * configureFields
	 *
	 * @param null $fields
	 *
	 * @return  array
	 */
	protected function configureFields($fields = null)
	{
		if ($fields && is_array($fields))
		{
			$this->fields = array_merge($this->fields, $fields);
		}

		return $this->fields;
	}
}
