<?php
/**
 * Part of windwalker_joomla_rad project. 
 *
 * @copyright  Copyright (C) 2011 - 2015 Achiever, Inc. All rights reserved.
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

		self::$instance = new \ArrayObject;
		self::$children['foo'] = new \ArrayObject;
	}

	public function getDataStore()
	{
		return $this->dataStore;
	}
}