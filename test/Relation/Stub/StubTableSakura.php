<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Relation\Stub;

use Windwalker\Table\Table;

/**
 * The StubTableSakura class.
 * 
 * @since  2.1
 */
class StubTableSakura extends Table
{
	/**
	 * Class init.
	 *
	 * @param \JDatabaseDriver $db
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__testflower_sakuras', 'id', $db);
	}
}
