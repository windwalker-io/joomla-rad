<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\DataMapper;

use Windwalker\Data\Data;
use Windwalker\DataMapper\DataMapperFacade;
use Windwalker\DI\Container;
use Windwalker\Relation\Action;
use Windwalker\Relation\Relation;
use Windwalker\Relation\RelationContainer;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\DataMapper\Stub\StubLocationDataMapper;
use Windwalker\Test\Relation\Stub\StubTableRose;
use Windwalker\Test\Relation\Stub\StubTableSakura;

/**
 * The RelationHandlerTest class.
 * 
 * @since  2.1
 */
class OneToManyRelationTest extends AbstractDatabaseTestCase
{
	/**
	 * createLocationTable
	 *
	 * @param string $onUpdate
	 * @param string $onDelete
	 * @param bool   $flush
	 *
	 * @return StubLocationDataMapper
	 */
	protected function createTestMapper($onUpdate = Action::CASCADE, $onDelete = Action::CASCADE, $flush = false)
	{
		$location = new StubLocationDataMapper;

		$location->relation->addOneToMany('sakuras', new StubTableSakura, array('id' => 'location'), $onUpdate, $onDelete, array('flush' => $flush));
		$location->relation->addOneToMany('roses', new StubTableRose, array('id' => 'location'), $onUpdate, $onDelete, array('flush' => $flush));

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
		$location = $this->createTestMapper();

		$dataset = $location->find(3);

		$data = $dataset[0];

		$sakuras = $data->sakuras;
		$roses = $data->roses;

		$this->assertInstanceOf('Windwalker\Data\Data', $sakuras[0]);
		$this->assertInstanceOf('Windwalker\Data\DataSet', $sakuras);
		$this->assertInstanceOf('Windwalker\Data\DataSet', $roses);
		$this->assertEquals(array(11, 12, 13, 14, 15), $sakuras->id);
		$this->assertEquals(array(11, 12, 13, 14, 15), $roses->id);
	}

	/**
	 * testGlobalRelationConfig
	 *
	 * @return  void
	 */
	public function testGlobalRelationConfig()
	{
		/** @var RelationContainer $relations */
		$relations = Container::getInstance()->get('relation.container');

		$relation = $relations->getRelation(static::TABLE_LOCATIONS);

		$relation->addOneToMany('sakuras', new StubTableSakura, array('id' => 'location'));
		$relation->addOneToMany('roses', new StubTableRose, array('id' => 'location'));

		$mapper = new StubLocationDataMapper;

		$dataset = $mapper->find(3);

		$data = $dataset[0];

		$sakuras = $data->sakuras;
		$roses = $data->roses;

		$this->assertInstanceOf('Windwalker\Data\Data', $sakuras[0]);
		$this->assertInstanceOf('Windwalker\Data\DataSet', $sakuras);
		$this->assertInstanceOf('Windwalker\Data\DataSet', $roses);
		$this->assertEquals(array(11, 12, 13, 14, 15), $sakuras->id);
		$this->assertEquals(array(11, 12, 13, 14, 15), $roses->id);

		$relations->setRelation(static::TABLE_LOCATIONS, new Relation);
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
		$mapper = $this->createTestMapper();
		$location = new Data;

		$sakura1 = new Data;
		$sakura1->title = 'Sakura Create 1';
		$sakura1->state = 1;

		$sakura2 = new Data;
		$sakura2->title = 'Sakura Create 2';
		$sakura2->state = 1;

		$location->sakuras = array($sakura1, $sakura2);

		$rose1 = new Data;
		$rose1->title = 'Rose Create 1';
		$rose1->state = 1;

		$rose2 = new Data;
		$rose2->title = 'Rose Create 2';
		$rose2->state = 1;

		$location->roses = array($rose1, $rose2);

		$location->title = 'Location Create 1';
		$location->state = 1;

		$mapper->create(array($location));
		$location2 = $mapper->findOne(array('title' => 'Location Create 1'));

		$this->assertTrue($location2->notNull());
		$this->assertInstanceOf('Windwalker\Data\Data', $location2->sakuras[0]);
		$this->assertInstanceOf('Windwalker\Data\Data', $location2->roses[1]);
		$this->assertEquals(array('Rose Create 1', 'Rose Create 2'), $location2->roses->title);

		$locationData = DataMapperFacade::findOne(static::TABLE_LOCATIONS, array(), 'id DESC');

		$this->assertEquals('Location Create 1', $locationData->title);
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
		// Only update self
		$mapper = $this->createTestMapper();

		$location = $mapper->findOne(1);

		$location->state = 0;

		$mapper->update(array($location));

		$location2 = $mapper->findOne(1);

		$this->assertEquals(0, $location2->state);
		$this->assertEquals($location->sakuras, $location2->sakuras);

		// Update relations
		$location = $mapper->findOne(1);

		$location->state = 2;

		$location->sakuras[2]->title = 'Sakura 3 Modified';
		$location->roses[1]->title = 'Rose 2 Modified';

		$mapper->update(array($location));

		$location2 = $mapper->findOne(1);

		$this->assertEquals(2, $location2->state);
		$this->assertEquals($location->sakuras, $location2->sakuras);

		$this->assertEquals('Sakura 3 Modified', DataMapperFacade::findOne(static::TABLE_SAKURAS, 3)->title);
		$this->assertEquals('Rose 2 Modified', DataMapperFacade::findOne(static::TABLE_ROSES, 2)->title);
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
		$mapper = $this->createTestMapper(Action::NO_ACTION);

		$location = $mapper->findOne(2);

		$location->state = 2;

		$location->sakuras[2]->title = 'Sakura 8 Modified';
		$location->roses[1]->title = 'Rose 7 Modified';

		$mapper->update(array($location));

		$location2 = $mapper->findOne(2);

		$this->assertEquals(2, $location2->state);
		$this->assertNotEquals($location->sakuras, $location2->sakuras);

		$this->assertEquals('Sakura 8', DataMapperFacade::findOne(static::TABLE_SAKURAS, 8)->title);
		$this->assertEquals('Rose 7', DataMapperFacade::findOne(static::TABLE_ROSES, 7)->title);
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
		$this->markTestSkipped('No necessary to test this because Table Relation tested.');
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
		$mapper = $this->createTestMapper();

		$location = $mapper->findOne(1);

		$sakuraIds = $location->sakuras->id;
		$roseIds = $location->roses->id;

		$mapper->delete(1);

		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_LOCATIONS, 1)->isNull());
		$this->assertEquals(array(1, 2, 3, 4, 5), $sakuraIds);
		$this->assertEquals(array(1, 2, 3, 4, 5), $roseIds);

		$this->assertTrue(DataMapperFacade::find(static::TABLE_SAKURAS, array('id' => $sakuraIds))->isNull());
		$this->assertTrue(DataMapperFacade::find(static::TABLE_ROSES, array('id' => $roseIds))->isNull());
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
		$this->markTestSkipped('No necessary to test this because Table Relation tested.');
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
		$this->markTestSkipped('No necessary to test this because Table Relation tested.');
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
		$mapper = $this->createTestMapper(Action::CASCADE, Action::CASCADE, true);

		$location = $mapper->findOne(4);

		$location->state = 7;

		$mapper->update(array($location));

		$location2 = $mapper->findOne(4);

		$this->assertEquals(7, $location2->state);
		$this->assertEquals(array(28, 29, 30, 31, 32), $location2->roses->id);
	}
}
