<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Model\ItemModel;

/**
 * Class {{extension.name.cap}}Model{{controller.item.name.cap}}
 *
 * @since 1.0
 */
class {{extension.name.cap}}Model{{controller.item.name.cap}} extends ItemModel
{
	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix = '{{extension.name.lower}}';

	/**
	 * Property option.
	 *
	 * @var  string
	 */
	protected $option = '{{extension.element.lower}}';

	/**
	 * Property textPrefix.
	 *
	 * @var string
	 */
	protected $textPrefix = '{{extension.element.upper}}';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = '{{controller.item.name.lower}}';

	/**
	 * Property viewList.
	 *
	 * @var  string
	 */
	protected $viewList = '{{controller.list.name.lower}}';
}
