<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Relation;

use Windwalker\Data\Data;
use Windwalker\DataMapper\DataMapperFacade;
use Windwalker\Relation\Action;
use Windwalker\Table\Table;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Relation\Stub\StubTableLocation;
use Windwalker\Test\Relation\Stub\StubTableRose;
use Windwalker\Test\Relation\Stub\StubTableSakura;

/**
 * The RelationHandlerTest class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class ManyToManyRelationTest extends AbstractDatabaseTestCase
{
	/**
	 * createLocationTable
	 *
	 * @param string $onUpdate
	 * @param string $onDelete
	 *
	 * @return  StubTableLocation
	 */
	protected function createTestTable($onUpdate = Action::CASCADE, $onDelete = Action::CASCADE)
	{
		$location = new StubTableSakura(\JFactory::getDbo());

		$location->_relation->addManyToMany('roses')
			->mappingTable(static::TABLE_SAKURA_ROSE_MAPS, array('id' => 'sakura_id'))
			->targetTable(new StubTableRose, array('rose_id' => 'id'))
			->onUpdate($onUpdate)
			->onDelete($onDelete);

		return $location;
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

		$roses = $sakura->roses;

		$this->assertEquals(array(2, 4, 9, 11, 15, 17), $roses->id);
	}

	/**
	 * testCreate
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::store
	 */
	public function testCreate()
	{
		$this->markTestIncomplete();
	}

	/**
	 * testUpdate
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::store
	 */
	public function testUpdate()
	{
		$this->markTestIncomplete();
	}

	/**
	 * testUpdateNoAction
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::store
	 */
	public function testUpdateNoAction()
	{
		$this->markTestIncomplete();
	}

	/**
	 * testUpdateSetNull
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::store
	 */
	public function testUpdateSetNull()
	{
		$this->markTestIncomplete();
	}

	/**
	 * testDelete
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::delete
	 */
	public function testDelete()
	{
		$this->markTestIncomplete();
	}

	/**
	 * testDeleteNoAction
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::delete
	 */
	public function testDeleteNoAction()
	{
		$this->markTestIncomplete();
	}

	/**
	 * testDeleteSetNull
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::delete
	 */
	public function testDeleteSetNull()
	{
		$this->markTestIncomplete();
	}
}
