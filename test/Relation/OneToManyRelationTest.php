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
use Windwalker\DI\Container;
use Windwalker\Relation\Action;
use Windwalker\Relation\Relation;
use Windwalker\Relation\RelationContainer;
use Windwalker\Table\Table;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
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
	 * @return Table
	 */
	protected function createTestTable($onUpdate = Action::CASCADE, $onDelete = Action::CASCADE, $flush = false)
	{
		$location = new Table(static::TABLE_LOCATIONS, 'id', \JFactory::getDbo());

		$location->_relation->addOneToMany('sakuras', new StubTableSakura, array('id' => 'location'), $onUpdate, $onDelete, array('flush' => $flush));
		$location->_relation->addOneToMany('roses', new StubTableRose, array('id' => 'location'), $onUpdate, $onDelete, array('flush' => $flush));

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
		$location = $this->createTestTable();

		$location->load(3);

		$sakuras = $location->sakuras;
		$roses = $location->roses;

		$this->assertInstanceOf('Windwalker\Data\Data', $sakuras[0]);
		$this->assertInstanceOf('Windwalker\Data\DataSet', $sakuras);
		$this->assertInstanceOf('Windwalker\Data\DataSet', $roses);
		$this->assertEquals(array(11, 12, 13, 14, 15), $sakuras->id);
		$this->assertEquals(array(11, 12, 13, 14, 15), $roses->id);
	}

	public function testGlobalRelationConfig()
	{
		/** @var RelationContainer $relations */
		$relations = Container::getInstance()->get('relation.container');

		$relation = $relations->getRelation(static::TABLE_LOCATIONS);

		$relation->addOneToMany('sakuras', new StubTableSakura, array('id' => 'location'));
		$relation->addOneToMany('roses', new StubTableRose, array('id' => 'location'));

		$location = new Table(static::TABLE_LOCATIONS, 'id', \JFactory::getDbo());

		$location->load(3);

		$sakuras = $location->sakuras;
		$roses = $location->roses;

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
		$location = $this->createTestTable();

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

		$location->store();

		$location2 = $this->createTestTable();
		$this->assertTrue($location2->load(array('title' => 'Location Create 1')));
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
		$location = $this->createTestTable();

		$location->load(1);

		$location->state = 0;

		$location->store();

		$location2 = $this->createTestTable();
		$location2->load(1);

		$this->assertEquals(0, $location2->state);
		$this->assertEquals($location->sakuras, $location2->sakuras);

		// Update relations
		$location = $this->createTestTable();

		$location->load(1);

		$location->state = 2;

		$location->sakuras[2]->title = 'Sakura 3 Modified';
		$location->roses[1]->title = 'Rose 2 Modified';

		$location->store();

		$location2 = $this->createTestTable();
		$location2->load(1);

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
		$location = $this->createTestTable(Action::NO_ACTION);

		$location->load(2);

		$location->state = 2;

		$location->sakuras[2]->title = 'Sakura 8 Modified';
		$location->roses[1]->title = 'Rose 7 Modified';

		$location->store();

		$location2 = $this->createTestTable();
		$location2->load(2);

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
		$location = $this->createTestTable(Action::SET_NULL);

		$location->load(5);

		$location->id = null;
		$sakuraIds = $location->sakuras->id;
		$roseIds = $location->roses->id;

		$location->store();

		$location2 = $this->createTestTable();
		$location2->load(7);

		$this->assertEquals(7, $location2->id);
		$this->assertNotEquals($location->sakuras, $location2->sakuras);

		$this->assertEquals(array(0, 0, 0, 0, 0), DataMapperFacade::find(static::TABLE_SAKURAS, array('id' => $sakuraIds))->location);
		$this->assertEquals(array(0, 0, 0, 0, 0), DataMapperFacade::find(static::TABLE_ROSES, array('id' => $roseIds))->location);
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
		$location = $this->createTestTable();

		$location->load(1);
		$sakuraIds = $location->sakuras->id;
		$roseIds = $location->roses->id;

		$location->delete();

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
		$location = $this->createTestTable(null, Action::NO_ACTION);

		$location->load(2);
		$sakuraIds = $location->sakuras->id;
		$roseIds = $location->roses->id;

		$location->delete();

		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_LOCATIONS, 2)->isNull());
		$this->assertEquals(array(6, 7, 8, 9, 10), $sakuraIds);
		$this->assertEquals(array(6, 7, 8, 9, 10), $roseIds);

		$sakuras = DataMapperFacade::find(static::TABLE_SAKURAS, array('id' => $sakuraIds));
		$roses = DataMapperFacade::find(static::TABLE_ROSES, array('id' => $roseIds));

		$this->assertEquals(array(6, 7, 8, 9, 10), $sakuras->id);
		$this->assertEquals(array(6, 7, 8, 9, 10), $roses->id);
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
		$location = $this->createTestTable(null, Action::SET_NULL);

		$location->load(3);
		$sakuraIds = $location->sakuras->id;
		$roseIds = $location->roses->id;

		$location->delete();

		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_LOCATIONS, 3)->isNull());
		$this->assertEquals(array(11, 12, 13, 14, 15), $sakuraIds);
		$this->assertEquals(array(11, 12, 13, 14, 15), $roseIds);

		$sakuras = DataMapperFacade::find(static::TABLE_SAKURAS, array('id' => $sakuraIds));
		$roses = DataMapperFacade::find(static::TABLE_ROSES, array('id' => $roseIds));

		$this->assertEquals(array(11, 12, 13, 14, 15), $sakuras->id);
		$this->assertEquals(array(11, 12, 13, 14, 15), $roses->id);

		$this->assertEquals(array(0, 0, 0, 0, 0), $sakuras->location);
		$this->assertEquals(array(0, 0, 0, 0, 0), $roses->location);
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
		$location = $this->createTestTable(Action::CASCADE, Action::CASCADE, true);

		$location->load(4);

		$location->state = 7;

		$location->store();

		$location2 = $this->createTestTable(Action::CASCADE, Action::CASCADE, true);

		$location2->load(4);

		$this->assertEquals(7, $location2->state);
		$this->assertEquals(array(28, 29, 30, 31, 32), $location2->roses->id);
	}
}
