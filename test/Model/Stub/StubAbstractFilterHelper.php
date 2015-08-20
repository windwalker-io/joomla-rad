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
 * @since 1.0
 */
class StubAbstractFilterHelper extends AbstractFilterHelper
{
	public function execute(\JDatabaseQuery $query, $data = array())
	{
		return true;
	}

	protected function registerDefaultHandler()
	{
		return function ($arg1, $arg2){
			return $arg1 * $arg2;
		};
	}
}
