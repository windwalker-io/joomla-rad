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
 * {{extension.name.cap}} {{controller.item.name.cap}} model
 *
 * @since 1.0
 */
class {{extension.name.cap}}Model{{controller.item.name.cap}} extends ItemModel
{
	/**
	 * Component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = '{{extension.name.lower}}';

	/**
	 * The URL option for the component.
	 *
	 * @var  string
	 */
	protected $option = '{{extension.element.lower}}';

	/**
	 * The prefix to use with messages.
	 *
	 * @var  string
	 */
	protected $textPrefix = '{{extension.element.upper}}';

	/**
	 * The model (base) name
	 *
	 * @var  string
	 */
	protected $name = '{{controller.item.name.lower}}';

	/**
	 * Item name.
	 *
	 * @var  string
	 */
	protected $viewItem = '{{controller.item.name.lower}}';

	/**
	 * List name.
	 *
	 * @var  string
	 */
	protected $viewList = '{{controller.list.name.lower}}';

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}
}
