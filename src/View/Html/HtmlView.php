<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View\Html;

use Joomla\DI\Container;
use Windwalker\Data\Data;
use Windwalker\Model\Model;
use Joomla\Registry\Registry;
use Windwalker\View\Helper\ToolbarHelper;

/**
 * Prototype html view.
 *
 * @since 2.0
 */
class HtmlView extends AbstractHtmlView
{
	/**
	 * The buttons cache.
	 *
	 * @var  array
	 */
	protected $buttons = array();

	/**
	 * The toolbar config.
	 *
	 * @var  array
	 */
	protected $toolbarConfig = array();

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

		$this->buttons = $this->buttons ? : \JArrayHelper::getValue($config, 'buttons', array());

		$this->toolbarConfig = $this->toolbarConfig ? : \JArrayHelper::getValue($config, 'toolbar', array());
	}

	/**
	 * Set title of this page.
	 *
	 * @param string $title Page title.
	 * @param string $icons Title icon.
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'stack')
	{
		$doc = $this->container->get('document');
		$doc->setTitle($title);

		if ($this->container->get('app')->isAdmin())
		{
			\JToolbarHelper::title($title, $icons);
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

		$data = $this->data;

		// View data
		$data->view = new Data;
		$data->view->prefix   = $this->prefix;
		$data->view->option   = $this->option;
		$data->view->name     = $this->getName();
		$data->view->viewItem = $this->viewItem;
		$data->view->viewList = $this->viewList;
		$data->view->layout   = $this->layout;

		// Uri data
		$uri = \JUri::getInstance();
		$data->uri = new Data;
		$data->uri->path = $uri->toString(array('path', 'query', 'fragment'));
		$data->uri->base = \JUri::base(true);
		$data->uri->root = \JUri::root(true);

		// Asset data
		$data->asset = $this->container->get('helper.asset');
	}

	/**
	 * Method to add toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		$toolbar = $this->getToolbarHelper($this->toolbarConfig, $this->buttons);

		$toolbar->registerButtons();
	}

	/**
	 * Method to get toolbar helper.
	 *
	 * We can send a basic config into it, there will have a default config and merge them.
	 *
	 * @param array $config    The customize config.
	 * @param array $buttonSet The customize button set.
	 *
	 * @return  ToolbarHelper
	 */
	protected function getToolbarHelper($config = array(), $buttonSet = array())
	{
		$component = $this->container->get('component');
		$canDo     = $component->getActions($this->viewItem);
		$buttonSet = $buttonSet ? : $this->configureToolbar($this->buttons, $canDo);

		$defaultConfig = array(
			'view_name' => $this->getName(),
			'view_item' => $this->viewItem,
			'view_list' => $this->viewList,
			'option'    => $this->option,
			'access'    => $component->getActions($this->viewItem),
		);

		$config = with(new Registry($defaultConfig))
			->loadArray($config);

		return new ToolbarHelper($this->data, $buttonSet, $config);
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
		return array();
	}
}
