<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Table\Stub;

use Windwalker\Table\Table;

/**
 * The StubTableRoses class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class StubTableRoses extends Table
{
	/**
	 * Class init.
	 *
	 * @param \JDatabaseDriver $db
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__testflower_roses', 'id', $db);
	}
}
