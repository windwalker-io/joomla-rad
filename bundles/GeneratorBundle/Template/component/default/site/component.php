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
 * Class {{extension.name.cap}}Component
 *
 * @since 1.0
 */
final class {{extension.name.cap}}Component extends \{{extension.name.cap}}\Component\{{extension.name.cap}}Component
{
	/**
	 * Property defaultController.
	 *
	 * @var string
	 */
	protected $defaultController = '{{controller.list.name.lower}}.display';

	/**
	 * init
	 *
	 * @return void
	 */
	protected function prepare()
	{
		parent::prepare();
	}
}
