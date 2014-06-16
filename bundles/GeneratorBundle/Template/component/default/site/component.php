<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} Component
 *
 * @since 1.0
 */
final class {{extension.name.cap}}Component extends \{{extension.name.cap}}\Component\{{extension.name.cap}}Component
{
	/**
	 * Default task name.
	 *
	 * @var string
	 */
	protected $defaultController = '{{controller.list.name.lower}}.display';

	/**
	 * Prepare hook of this component.
	 *
	 * Do some customize initialise through extending this method.
	 *
	 * @return void
	 */
	public function prepare()
	{
		parent::prepare();
	}
}
