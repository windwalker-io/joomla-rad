<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use {{extension.name.cap}}\Router\Route;
use Windwalker\Data\Data;
use Windwalker\View\Helper\FrontViewHelper;
use Windwalker\View\Html\ListHtmlView;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} {{controller.list.name.cap}} View
 *
 * @since 1.0
 */
class {{extension.name.cap}}View{{controller.list.name.cap}}Html extends ListHtmlView
{
	/**
	 * The component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = '{{extension.name.lower}}';

	/**
	 * The component option name.
	 *
	 * @var string
	 */
	protected $option = '{{extension.element.lower}}';

	/**
	 * The text prefix for translate.
	 *
	 * @var  string
	 */
	protected $textPrefix = '{{extension.element.upper}}';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $name = '{{controller.list.name.lower}}';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $viewItem = '{{controller.item.name.lower}}';


	/**
	 * Prepare data hook.
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		/** @var {{extension.name.cap}}Model{{controller.list.name.cap}}*/
		$this['params']   = $this->get('Params');
		$this['category'] = $this->get('Category');

		// Uncomment this to fix Joomla pagination routing
		// $this['pagination']->setAdditionalUrlParam('_resource', '{{controller.list.name.lower}}');

		// Set Data
		// =====================================================================================
		foreach ($this->data->items as &$item)
		{
			$item = new Data($item);
			$item->params = new JRegistry($item->params);

			$item->text = $item->introtext;

			// Link
			// =====================================================================================
			$item->link = Route::_('{{controller.item.name.lower}}', array(
				'id'    => $item->id,
				'alias' => $item->alias,
				// 'catid' => $item->catid
			));

			// Publish Date
			// =====================================================================================
			FrontViewHelper::checkPublishedDate($item);

			// Plugins
			// =====================================================================================
			FrontViewHelper::events($item, $this['params'], $this->context);
		}

		// Set title
		// =====================================================================================
		$this->configureTitle();
	}

	/**
	 * configureTitle
	 *
	 * @return  void
	 */
	public function configureTitle()
	{
		$app = $this->container->get('app');
		$active = $app->getMenu()->getActive();

		if ($active)
		{
			$currentLink = $active->link;

			if (!strpos($currentLink, 'view={{controller.list.name.lower}}') || !(strpos($currentLink, 'id=' . (string) $this['category']->id)))
			{
				// If not Active, set Title
				$this->setTitle($this['category']->title);
			}

			// Otherwise use Menu title.
		}
		else
		{
			$this->setTitle($this['category']->title);
		}
	}
}
