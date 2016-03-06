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
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Relation\Stub\StubTableLocation;

/**
 * The OneToOneRelationTest class.
 * 
 * @since  2.1
 */
class OneToOneRelationTest extends AbstractDatabaseTestCase
{
	/**
	 * createLocationTable
	 *
	 * @param string $onUpdate
	 * @param string $onDelete
	 *
	 * @return  StubTableLocation
	 */
	protected function createTestTable($onUpdate = Action::CASCADE, $onDelete = Action::CASCADE, $flush = false)
	{
		$location = new StubTableLocation(\JFactory::getDbo());

		$location->_relation->getRelation('data')
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
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::load
	 */
	public function testLoad()
	{
		$location = $this->createTestTable();

		$location->load(1);

		$data = $location->data;

		$this->assertInstanceOf('Windwalker\Data\Data', $data);
		$this->assertEquals(6, $data->id);

		$this->assertEquals('「至難得者，謂操曰：運籌決算有神功，二虎還須遜一龍。初到任，即設五色棒十餘條於縣之四門。有犯禁者，。', $data->data);
	}

	/**
	 * testCreate
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::store
	 */
	public function testCreate()
	{
		$location = $this->createTestTable();

		$location->title = 'Location Create 1';

		$data = new Data;
		$data->data = 'Location Data Create 1';

		$location->data = $data;

		$location->store();

		$location2 = $this->createTestTable();
		$location2->load(array('title' => 'Location Create 1'));

		$this->assertEquals(11, $location2->data->id);
		$this->assertEquals(6, $location2->data->location_id);
		$this->assertEquals('Location Data Create 1', $location2->data->data);

		$this->assertEquals('Location Data Create 1', DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, array('location_id' => 6))->data);
	}

	/**
	 * testUpdate
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::store
	 */
	public function testUpdate()
	{
		$location = $this->createTestTable();

		$location->load(1);

		$location->state = 2;

		$location->data->data = '123';

		$location->store();

		$location2 = $this->createTestTable();

		$location2->load(1);

		$this->assertEquals(2, $location2->state);
		$this->assertEquals($location->data, $location2->data);

		$this->assertEquals('123', DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, 6)->data);
	}

	/**
	 * testUpdateNoAction
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::store
	 */
	public function testUpdateNoAction()
	{
		$location = $this->createTestTable(Action::NO_ACTION);

		$location->load(1);

		$location->id = null;
		$location->state = 1;

		$location->data->data = 'Gandalf';

		$location->store();

		$location2 = $this->createTestTable();

		$location2->load(7);

		$this->assertEquals(1, $location2->state);
		$this->assertNotEquals($location->data, $location2->data);

		$this->assertEquals('123', DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, 6)->data);
	}

	/**
	 * testUpdateSetNull
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::store
	 */
	public function testUpdateSetNull()
	{
		$location = $this->createTestTable(Action::SET_NULL);

		$location->load(2);

		$location->id = null;
		$location->state = 2;

		$location->data->data = 'Aragorn';

		$location->store();

		$location2 = $this->createTestTable(Action::SET_NULL);

		$location2->load(8);

		$this->assertEquals(2, $location2->state);
		$this->assertTrue($location2->data->isNull());

		$this->assertEquals(0, DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, 7)->location_id);
	}

	/**
	 * testDelete
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::delete
	 */
	public function testDelete()
	{
		$location = $this->createTestTable();

		$location->load(3);
		$dataId = $location->data->id;

		$location->delete();

		$this->assertEquals(8, $dataId);
		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_LOCATIONS, 3)->isNull());
		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, $dataId)->isNull());
	}

	/**
	 * testDeleteNoAction
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::delete
	 */
	public function testDeleteNoAction()
	{
		$location = $this->createTestTable(Action::NO_ACTION);

		$location->load(4);

		$dataId = $location->data->id;

		$location->delete();

		$this->assertEquals(9, $dataId);
		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_LOCATIONS, 4)->isNull());
		$this->assertEquals(4, DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, $dataId)->location_id);
		$this->assertEquals(
			'壘。汝可引本部五百餘人，以天書三卷授之，曰：「此張角正殺敗董卓回寨。玄德謂關、張寶勢窮力乏，必獲惡。',
			DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, $dataId)->data
		);
	}

	/**
	 * testDeleteSetNull
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Relation\Handler\OneToOneRelation::delete
	 */
	public function testDeleteSetNull()
	{
		$location = $this->createTestTable(Action::SET_NULL);

		$location->load(5);

		$dataId = $location->data->id;

		$location->delete();

		$this->assertEquals(10, $dataId);
		$this->assertTrue(DataMapperFacade::findOne(static::TABLE_LOCATIONS, 5)->isNull());
		$this->assertNull(DataMapperFacade::findOne(static::TABLE_LOCATION_DATA, $dataId)->location_id);
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

		$location->load(1);

		$location->state = 7;

		$location->store();

		$location2 = $this->createTestTable(Action::CASCADE, Action::CASCADE, true);

		$location2->load(1);

		$this->assertEquals(7, $location2->state);
		$this->assertEquals(12, $location2->data->id);
	}
}
