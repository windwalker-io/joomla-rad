<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\Engine\PhpEngine;
use Windwalker\View\Html\GridView;
use Windwalker\Xul\XulEngine;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} {{controller.list.name.cap}} View
 *
 * @since 1.0
 */
class {{extension.name.cap}}View{{controller.list.name.cap}}Html extends GridView
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
	 * The list name.
	 *
	 * @var  string
	 */
	protected $viewList = '{{controller.list.name.lower}}';

	/**
	 * Method to instantiate the view.
	 *
	 * @param Model            $model     The model object.
	 * @param Container        $container DI Container.
	 * @param array            $config    View config.
	 * @param SplPriorityQueue $paths     Paths queue.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array(), \SplPriorityQueue $paths = null)
	{
		$config['grid'] = array(
			// Some basic setting
			'option'    => '{{extension.element.lower}}',
			'view_name' => '{{controller.item.name.lower}}',
			'view_item' => '{{controller.item.name.lower}}',
			'view_list' => '{{controller.list.name.lower}}',

			// Column which we allow to drag sort
			'order_column'   => '{{controller.item.name.lower}}.catid, {{controller.item.name.lower}}.ordering',

			// Table id
			'order_table_id' => '{{controller.item.name.lower}}List',

			// Ignore user access, allow all.
			'ignore_access'  => false
		);

		// Directly use php engine
		$this->engine = new PhpEngine;

		parent::__construct($model, $container, $config, $paths);
	}

	/**
	 * Prepare data hook.
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
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
		// Get default button set.
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		// In debug mode, we remove trash button but use delete button instead.
		if (JDEBUG)
		{
			$buttonSet['trash']['access']  = false;
			$buttonSet['delete']['access'] = true;
		}

		return $buttonSet;
	}
}
