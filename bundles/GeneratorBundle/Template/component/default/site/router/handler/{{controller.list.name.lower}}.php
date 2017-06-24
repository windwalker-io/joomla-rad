<?php
/**
 * Part of rad project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\Router\Handler\AbstractCategoryHandler;

/**
 * The {{extension.name.cap}}RouterHandler{{controller.list.name.cap}} class.
 *
 * @since  1.0
 */
class {{extension.name.cap}}RouterHandler{{controller.list.name.cap}} extends AbstractCategoryHandler
{
	/**
	 * Property name.
	 *
	 * @var string
	 */
	protected static $name = '{{controller.list.name.lower}}';

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
			->setNestable()
			->addLayout('default');
	}
}
