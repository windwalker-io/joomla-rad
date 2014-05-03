<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\Controller\Resolver\ControllerDelegator;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} {{controller.list.name.cap}} delegator.
 *
 * @since 1.0
 */
class {{extension.name.cap}}Controller{{controller.list.name.cap}}Delegator extends ControllerDelegator
{
	/**
	 * Register aliases.
	 *
	 * @return  void
	 */
	protected function registerAliases()
	{
	}

	/**
	 * Create Controller.
	 *
	 * @param   string $class Controller class name.
	 *
	 * @return  \Windwalker\Controller\Controller Controller instance.
	 */
	protected function createController($class)
	{
		return parent::createController($class);
	}
}
