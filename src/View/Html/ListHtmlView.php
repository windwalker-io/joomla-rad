<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\View\Html;

use Windwalker\String\StringInflector as Inflector;;
use Windwalker\Model\Model;
use Windwalker\DI\Container;

/**
 * The list view.
 *
 * @since 2.0
 */
class ListHtmlView extends HtmlView
{
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

		// Guess the item view as the context.
		if (empty($this->viewList))
		{
			$this->viewList = $this->getName();
		}

		// Guess the list view as the plural of the item view.
		if (empty($this->viewItem))
		{
			$inflector = Inflector::getInstance();

			$this->viewItem = $inflector->toSingular($this->viewList);
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

		$this['items']      = $this['items'] ? : $this->get('Items');
		$this['pagination'] = $this['pagination'] ? : $this->get('Pagination');

		if ($errors = $this['state']->get('errors'))
		{
			$this->addMessage($errors);
		}

		// B/C for old templates
		foreach ($this['items'] as $item)
		{
			$pkName = strtolower($this->viewItem) . '_id';
			$item->$pkName = isset($item->id) ? $item->id : null;
		}
	}
}
