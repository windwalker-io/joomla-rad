<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use {{extension.name.cap}}\Component\{{extension.name.cap}}Component as {{extension.name.cap}}ComponentBase;

// No direct access
defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} Admin Component
 *
 * @since 1.0
 */
final class {{extension.name.cap}}Component extends {{extension.name.cap}}ComponentBase
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
	protected function prepare()
	{
		parent::prepare();
	}
}
