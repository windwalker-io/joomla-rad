<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Model\ItemModel;

// No direct access
defined('_JEXEC') or die;

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
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name = '{{controller.item.name.lower}}';

	/**
	 * Property viewItem.
	 *
	 * @var  string
	 */
	protected $viewItem = '{{controller.item.name.lower}}';
}
