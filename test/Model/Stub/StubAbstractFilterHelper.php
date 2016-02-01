<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Model\Stub;

use Windwalker\Model\Filter\AbstractFilterHelper;

/**
 * Class StubAbstractFilterHelper
 *
 * @since 2.1
 */
class StubAbstractFilterHelper extends AbstractFilterHelper
{
	/**
	 * execute
	 *
	 * @param \JDatabaseQuery $query
	 * @param array           $data
	 *
	 * @return  bool
	 */
	public function execute(\JDatabaseQuery $query, $data = array())
	{
		return true;
	}

	/**
	 * registerDefaultHandler
	 *
	 * @return  \Closure
	 */
	protected function registerDefaultHandler()
	{
		return function ($arg1, $arg2)
		{
			return $arg1 * $arg2;
		};
	}
}
