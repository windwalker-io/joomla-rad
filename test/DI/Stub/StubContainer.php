<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\DI\Stub;

use Windwalker\DI\Container;

/**
 * Class StubContainer
 *
 * @since 1.0
 */
class StubContainer extends Container
{
	/**
	 * StubContainer constructor
	 *
	 * @param Container $parent
	 */
	function __construct(Container $parent = null)
	{
		parent::__construct($parent);

		// Add fake root $instance and children
		self::$instance = new \ArrayObject;
		self::$children['foo'] = new \ArrayObject;
	}

	/**
	 * getParent
	 *
	 * @return  \Joomla\DI\Container
	 */
	public function getParent()
	{
		return $this->parent;
	}
}
