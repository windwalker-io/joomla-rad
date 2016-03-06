<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Relation;

use Windwalker\Relation\Action;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Relation\Stub\StubTableLocation;
use Windwalker\Test\Relation\Stub\StubTableSakura;

/**
 * The ManyRoOneRelationTest class.
 * 
 * @since  2.1
 */
class ManyRoOneRelationTest extends AbstractDatabaseTestCase
{
	/**
	 * createLocationTable
	 *
	 * @param string $onUpdate
	 * @param string $onDelete
	 *
	 * @return  StubTableSakura
	 */
	protected function createTestTable($onUpdate = Action::NO_ACTION, $onDelete = Action::NO_ACTION)
	{
		$table = new StubTableSakura(\JFactory::getDbo());

		$table->_relation->addManyToOne('parent', new StubTableLocation, array('location' => 'id'), $onUpdate, $onDelete);

		return $table;
	}

	/**
	 * testLoad
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::load
	 */
	public function testLoad()
	{
		$sakura = $this->createTestTable();

		$sakura->load(1);

		$this->assertEquals('Sakura 1', $sakura->title);
		$this->assertEquals('雲彩裡', $sakura->parent->title);
	}
}
