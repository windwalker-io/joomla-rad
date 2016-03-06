<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

use {{extension.name.cap}}\Router\Route;
use Windwalker\Data\Data;
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

		// Prepare setting data
		$item = $this['item'] = new Data($this['item']);

		// Link
		// =====================================================================================
		$item->link = Route::_('{{extension.element.lower}}.{{controller.item.name.lower}}', array(
			'id'    => $item->id,
			'alias' => $item->alias
		));

		$item->text = $item->introtext . $item->fulltext;
	}
}
