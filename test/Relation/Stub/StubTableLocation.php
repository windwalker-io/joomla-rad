<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Relation\Stub;

use Windwalker\Table\Table;
use Windwalker\Test\Database\AbstractDatabaseTestCase;

/**
 * The StubTableLocation class.
 * 
 * @since  2.1
 */
class StubTableLocation extends Table
{
	/**
	 * Class init.
	 *
	 * @param \JDatabaseDriver $db
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__testflower_locations', 'id', $db);
	}

	/**
	 * configure
	 *
	 * @return  void
	 */
	protected function configure()
	{
		$this->_relation->addOneToOne('data', new Table(AbstractDatabaseTestCase::TABLE_LOCATION_DATA), array('id' => 'location_id'));
	}
}
