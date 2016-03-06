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
use Windwalker\Relation\Handler\ManyToManyRelation;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Relation\Stub\StubTableRose;
use Windwalker\Test\Relation\Stub\StubTableSakura;

/**
 * The RelationHandlerTest class.
 * 
 * @since  2.1
 */
class ManyToManyRelationTest extends AbstractDatabaseTestCase
{
	/**
	 * createLocationTable
	 *
	 * @param string $onUpdate
	 * @param string $onDelete
	 *
	 * @return  StubTableSakura
	 */
	protected function createTestTable($onUpdate = Action::CASCADE, $onDelete = Action::CASCADE, $flush = false)
	{
		$location = new StubTableSakura(\JFactory::getDbo());

		$location->_relation->addManyToMany('roses')
			->mappingTable(static::TABLE_SAKURA_ROSE_MAPS, array('id' => 'sakura_id'))
			->targetTable(new StubTableRose, array('rose_id' => 'id'))
			->onUpdate($onUpdate)
			->onDelete($onDelete)
			->flush($flush);

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
		$sakura = $this->createTestTable();

		$sakura->title = 'Sakura X';
		$sakura->state = 5;

		$rose1 = new Data;
		$rose1->title = 'Rose Y';
		$rose1->state = 1;

		$rose2 = new Data;
		$rose2->title = 'Rose Z';
		$rose2->state = 2;

		$sakura->roses = array($rose1, $rose2);

		$sakura->store();

		$sakura2 = $this->createTestTable();

		$sakura2->load(array('title' => 'Sakura X'));

		$this->assertEquals(26, $sakura2->id);

		$this->assertEquals(array(26, 27), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 26))->rose_id);
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
		$sakura = $this->createTestTable();

		$sakura->load(2);

		// Rose 3
		$sakura->roses = array($sakura->roses[0]);

		$sakura->state = 5;

		$sakura->store();

		// Make sure we only have one rose_id = 3, that means old map will be delete and create new one.
		$this->assertEquals(array(4, 19, 12, 22, 23, 25, 3), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 2))->rose_id);
		$this->assertEquals(5, DataMapperFacade::findOne(static::TABLE_SAKURAS, 2)->state);

		$rose = new Data;
		$rose->title = 'Rose U';
		$rose->state = 2;

		$sakura->roses[] = $rose;

		$sakura->roses[0]->state = 7;

		$sakura->store();

		// Create a new map
		$this->assertEquals(array(4, 19, 12, 22, 23, 25, 3, 28), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 2))->rose_id);
		$this->assertEquals('Rose U', DataMapperFacade::findOne(static::TABLE_ROSES, array('id' => 28))->title);
		$this->assertEquals(7, DataMapperFacade::findOne(static::TABLE_ROSES, array('id' => 3))->state);
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
		$sakura = $this->createTestTable(Action::NO_ACTION);
		$expect = $this->createTestTable(Action::NO_ACTION);

		$sakura->load(3);
		$expect->load(3);

		$sakura->state = 4;

		$rose = new Data;
		$rose->title = 'Rose V';
		$rose->state = 2;

		$sakura->roses[] = $rose;

		$sakura->roses[0]->state = 7;
		$sakura->roses[1]->state = 7;

		$sakura->store();

		$sakura2 = $this->createTestTable(Action::NO_ACTION);

		$sakura2->load(3);

		$this->assertEquals($expect->roses, $sakura2->roses);
		$this->assertEquals(array(16, 25), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 3))->rose_id);
		$this->assertEquals(4, DataMapperFacade::findOne(static::TABLE_SAKURAS, 3)->state);
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
		$sakura = $this->createTestTable(Action::SET_NULL);

		$sakura->load(4);

		$this->assertEquals(array(21, 24, 4, 1), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 4))->rose_id);

		$sakura->id = null;

		$sakura->store();

		$this->assertEquals(array(), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 4 ))->rose_id);
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
		$sakura = $this->createTestTable();

		$sakura->load(6);

		$this->assertEquals(array(12, 16, 4, 2, 18, 19), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 6))->rose_id);

		$sakura->delete();

		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_SAKURAS, 6)->isNull());
		$this->assertEquals(array(), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 6))->rose_id);
		$this->assertTrue(DataMapperFacade::find(static::TABLE_ROSES, array('id' => array(12, 16, 4, 2, 18, 19)))->isNull());
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
		$sakura = $this->createTestTable(null, Action::NO_ACTION);

		$sakura->load(7);

		$this->assertEquals(array(11, 1, 14), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 7))->rose_id);

		$sakura->delete();

		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_SAKURAS, 7)->isNull());
		$this->assertEquals(array(11, 1, 14), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 7))->rose_id);
		$this->assertTrue(DataMapperFacade::find(static::TABLE_ROSES, array('id' => array(11, 1, 14)))->notNull());
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
		$sakura = $this->createTestTable(null, Action::SET_NULL);

		$sakura->load(12);

		$this->assertEquals(array(25, 15, 8, 16, 24), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 12))->rose_id);

		$sakura->delete();

		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_SAKURAS, 12)->isNull());
		$this->assertEquals(array(), DataMapperFacade::find(static::TABLE_SAKURA_ROSE_MAPS, array('sakura_id' => 12))->rose_id);
		$this->assertTrue(DataMapperFacade::find(static::TABLE_ROSES, array('id' => array(25, 15, 8, 16, 24)))->notNull());
	}

	/**
	 * testUpdateFlush
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToManyRelation::store
	 */
	public function testUpdateFlush()
	{
		$sakura = $this->createTestTable(Action::CASCADE, Action::CASCADE, true);

		$sakura->load(15);

		$sakura->state = 7;

		$sakura->store();

		$sakura2 = $this->createTestTable(Action::CASCADE, Action::CASCADE, true);

		$sakura2->load(15);

		$this->assertEquals(7, $sakura2->state);
		$this->assertNotEquals($sakura->roses->id, $sakura2->roses->id);
	}

	/**
	 * testBuildMapQuery
	 *
	 * @return  void
	 */
	public function testBuildMapQuery()
	{
		$sakura = $this->createTestTable();

		$sakura->load(17);
		$sakura->state = 5;

		/** @var ManyToManyRelation $relation */
		$relation = $sakura->_relation->getRelation('roses');

		$relation->mappingTableForeignKeys(array('id' => 'sakura_id', 'state' => 'rose_id'));

		$sql = <<<SQL
SELECT *
FROM #__testflower_sakura_rose_maps
WHERE `sakura_id` = '17' AND `rose_id` = '5'
SQL;

		$this->assertStringDataEquals($sql, (string) $relation->buildMapQuery());
	}

	/**
	 * testBuildTargetQuery
	 *
	 * @return  void
	 */
	public function testBuildTargetQuery()
	{
		$sakura = $this->createTestTable();

		$sakura->load(17);
		$sakura->state = 5;

		/** @var ManyToManyRelation $relation */
		$relation = $sakura->_relation->getRelation('roses');
		$relation->foreignKeys(array('id' => 'sakura_id', 'state' => 'rose_id'));

		$mapping = array(
			(object) array(
				'id' => 1,
				'state' => 2
			),
			(object) array(
				'id' => 3,
				'state' => 4
			),
			(object) array(
				'id' => 5,
				'state' => 6
			),
		);

		$sql = <<<SQL
SELECT *
FROM #__testflower_roses
WHERE
(`sakura_id` = '1' AND `rose_id` = '2') OR
(`sakura_id` = '3' AND `rose_id` = '4') OR
(`sakura_id` = '5' AND `rose_id` = '6')
SQL;

		$this->assertStringDataEquals($sql, (string) $relation->buildTargetQuery($mapping));
	}
}
