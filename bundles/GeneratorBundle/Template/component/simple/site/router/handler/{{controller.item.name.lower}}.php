<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

use Windwalker\Router\Handler\AbstractItemHandler;

/**
 * The {{extension.name.cap}}RouterHandler{{controller.item.name.cap}} class.
 *
 * @since  1.0
 */
class {{extension.name.cap}}RouterHandler{{controller.item.name.cap}} extends AbstractItemHandler
{
	/**
	 * Property name.
	 *
	 * @var string
	 */
	protected static $name = '{{controller.item.name.lower}}';

	/**
	 * Property table.
	 *
	 * @var  string
	 */
	protected static $table = '#__{{extension.name.lower}}_{{controller.list.name.lower}}';

	/**
	 * Property noID.
	 *
	 * @var  bool
	 */
	protected static $noIDs = false;

	/**
	 * Configure view configuration.
	 *
	 * @param \JComponentRouterViewconfiguration $view
	 *
	 * @return  void
	 */
	public function configure(\JComponentRouterViewconfiguration $view)
	{
		$view->setKey('id')
			->setParent($this->router->getView('{{controller.list.name.lower}}'), 'catid')
			->addLayout('default');
	}
}
