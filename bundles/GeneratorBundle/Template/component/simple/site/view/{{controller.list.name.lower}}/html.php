<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use {{extension.name.cap}}\Router\Route;
use Windwalker\Data\Data;
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

		// Set Data
		// =====================================================================================
		foreach ($this->data->items as &$item)
		{
			$item = new Data($item);

			$item->text = $item->introtext;

			// Link
			// =====================================================================================
			$item->link = Route::_('{{controller.item.name.lower}}', array(
				'id'    => $item->id,
				'alias' => $item->alias
			));
		}
	}
}
