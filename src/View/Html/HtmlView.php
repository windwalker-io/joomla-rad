<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\View\Html;

use Joomla\DI\Container;
use Windwalker\Data\Data;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Model\Model;
use Windwalker\Registry\Registry;
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

		$this->buttons = $this->buttons ? : ArrayHelper::getValue($config, 'buttons', array());

		$this->toolbarConfig = $this->toolbarConfig ? : ArrayHelper::getValue($config, 'toolbar', array());
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

		// State
		$this['state'] = $this['state'] ? : $this->get('State');

		// View data
		if (!$this['view'])
		{
			$this['view'] = new Data;
			$this['view']->prefix   = $this->prefix;
			$this['view']->option   = $this->option;
			$this['view']->name     = $this->getName();
			$this['view']->viewItem = $this->viewItem;
			$this['view']->viewList = $this->viewList;
			$this['view']->layout   = $this->layout;
		}

		// Uri data
		if (!$this['uri'])
		{
			$uri = \JUri::getInstance();
			$this['uri'] = new Data;
			$this['uri']->path = $uri->toString(array('path', 'query', 'fragment'));
			$this['uri']->base = \JUri::base(true);
			$this['uri']->root = \JUri::root(true);
		}

		// Asset data
		$this['asset'] = $this['asset'] ? : $this->container->get('helper.asset');
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
