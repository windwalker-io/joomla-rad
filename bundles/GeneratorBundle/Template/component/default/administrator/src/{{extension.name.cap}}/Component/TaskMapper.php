<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace {{extension.name.cap}}\Component;

/**
 * Class TaskMapper
 *
 * @since 1.0
 */
class TaskMapper
{
	/**
	 * Property component.
	 *
	 * @var  \{{extension.name.cap}}\Component\{{extension.name.cap}}Component
	 */
	protected $component = null;

	/**
	 * Class init.
	 *
	 * @param $component
	 */
	public function __construct($component)
	{
		$this->component = $component;
	}

	/**
	 * Register task to controller class name.
	 *
	 * Example: `$this->component->registerTask('queue.execute', '\\{{extension.name.cap}}\\Controller\\Queue\\ExecuteController');`
	 *
	 * @return  void
	 */
	public function register()
	{
		// Register task here
	}
}
 