<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use {{extension.name.cap}}\Router\Route;
use Joomla\Registry\Registry;
use Windwalker\Data\Data;
use Windwalker\DataMapper\DataMapper;
use Windwalker\View\Helper\FrontViewHelper;
use Windwalker\View\Html\ItemHtmlView;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} {{controller.list.name.cap}} view
 *
 * @since 1.0
 */
class {{extension.name.cap}}View{{controller.item.name.cap}}Html extends ItemHtmlView
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
	protected $name = '{{controller.item.name.lower}}';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $viewItem = '{{controller.item.name.lower}}';

	/**
	 * The list name.
	 *
	 * @var  string
	 */
	protected $viewList = '{{controller.list.name.lower}}';

	/**
	 * Prepare data hook.
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		/** @var {{extension.name.cap}}Model{{controller.item.name.cap}} */
		$this['category'] = $this->get('Category');
		$this['params'] = $this->get('Params');

		// Prepare setting data
		$item = $this['item'] = new Data($this['item']);
		$state = $this['state'];

		// Link
		// =====================================================================================
		$item->link = Route::_('{{extension.element.lower}}.{{controller.item.name.lower}}', array(
			'id'    => $item->id,
			'alias' => $item->alias,
			// 'catid' => $item->catid
		));

		// Dsplay Data
		// =====================================================================================
		$item->user_name = with(new DataMapper('#__users'))->findOne($item->created_by)->name;
		$item->cat_title = !empty($this->category) ? $this->category->title : null;

		$item->text = $item->introtext . $item->fulltext;

		// View Level
		// =====================================================================================
		FrontViewHelper::viewLevel($item, $this['category'], $state, $this['params']);

		// Publish Date
		// =====================================================================================
		FrontViewHelper::checkPublishedDate($item);

		// Plugins
		// =====================================================================================
		FrontViewHelper::events($item, $this['params'], $this->context);

		$this->configureParams($item);
	}

	/**
	 * Configure the config data.
	 *
	 * @param Data $item The item object
	 *
	 * @return  void
	 */
	protected function configureParams($item)
	{
		$app  = $this->container->get('app');
		$data = $this->getData();

		// Params
		// =====================================================================================

		// Merge {{controller.item.name.lower}} params. If this is single-{{controller.item.name.lower}} view, menu params override article params
		// Otherwise, {{controller.item.name.lower}} params override menu item params
		$active       = $app->getMenu()->getActive();
		$temp         = clone ($data->params);
		$item->params = new Registry($item->params);

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;

			// If the current view is the active item and an {{controller.item.name.lower}} view for this {{controller.item.name.lower}},
			// then the menu item params take priority
			if (strpos($currentLink, 'view={{controller.item.name.lower}}') && (strpos($currentLink, '&id=' . (string) $item->id)))
			{
				// $item->params are the {{controller.item.name.lower}} params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);

				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
			}
			else
			{
				// Current view is not a single {{controller.item.name.lower}}, so the {{controller.item.name.lower}} params take priority here
				// Merge the menu item params with the {{controller.item.name.lower}} params so that the {{controller.item.name.lower}} params take priority
				$temp->merge($item->params);
				$this->params = $temp;

				// Check for alternative layouts (since we are not in a single-{{controller.item.name.lower}} menu item)
				// Single-{{controller.item.name.lower}} menu item layout takes priority over alt layout for an {{controller.item.name.lower}}
				if ($layout = $data->params->get('layout_type'))
				{
					$this->setLayout($layout);
				}

				// If not Active, set Title
				$this->setTitle($item->get('title'));
			}
		}
		else
		{
			// Merge so that article params take priority
			$temp->merge($data->params);
			$this->params = $temp;

			// Check for alternative layouts (since we are not in a single-article menu item)
			// Single-article menu item layout takes priority over alt layout for an article
			if ($layout = $data->params->get('layout_type'))
			{
				$this->setLayout($layout);
			}

			// If not Active, set Title
			$this->setTitle($item->get('title'));
		}

		$item->params = $data->params;
	}
}
