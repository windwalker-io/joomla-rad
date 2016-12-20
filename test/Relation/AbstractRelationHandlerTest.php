<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Relation;

use Windwalker\Data\Data;
use Windwalker\Relation\Action;
use Windwalker\Table\Table;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Joomla\MockDatabaseDriver;
use Windwalker\Test\Relation\Stub\StubRelationHandler;
use Windwalker\Test\Relation\Stub\StubTableRose;
use Windwalker\Test\Relation\Stub\StubTableSakura;

/**
 * Test class of \Windwalker\Relation\Handler\AbstractRelationHandler
 *
 * @since 2.1
 */
class AbstractRelationHandlerTest extends AbstractDatabaseTestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Relation\Handler\AbstractRelationHandler
	 */
	protected $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$parent = new StubTableSakura;
		$parent->foo = 'Foo';
		$parent->bar = 'Bar';
		$parent->roses = 'Roses';

		$this->instance = new StubRelationHandler($parent, 'roses');

		$this->instance->targetTable(new StubTableRose, array('foo' => 'foo_id', 'bar' => 'bar_id'));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}
	
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::__construct
	 */
	public function test__construct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getParentFieldValue().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getParentFieldValue
	 */
	public function testGetParentFieldValue()
	{
		$parent = new StubTableSakura;
		$parent->roses = 'roses';

		$this->instance->parent($parent);

		$this->assertEquals('roses', $this->instance->getParentFieldValue());
	}

	/**
	 * Method to test setParentFieldValue().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::setParentFieldValue
	 */
	public function testSetParentFieldValue()
	{
		$parent = new StubTableSakura;

		$this->instance->parent($parent);

		$this->instance->setParentFieldValue('foo');

		$this->assertEquals('foo', $parent->roses);
	}

	/**
	 * Method to test deleteAllRelatives().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::deleteAllRelatives
	 */
	public function testDeleteAllRelatives()
	{
		$db = new MockDatabaseDriver;

		$this->instance->setDb($db);

		$this->instance->deleteAllRelatives();

		$sql = <<<SQL
DELETE
FROM #__testflower_roses
WHERE foo_id = 'Foo'
AND bar_id = 'Bar'
SQL;

		$this->assertStringDataEquals($sql, $db->lastQuery);
	}

	/**
	 * Method to test handleUpdateRelations().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::handleUpdateRelations
	 */
	public function testHandleUpdateRelations()
	{
		$itemTable = new StubTableRose;
		$expectedTable = new StubTableRose;

		// CASCADE
		$this->instance->handleUpdateRelations($itemTable);

		$expectedTable->foo_id = 'Foo';
		$expectedTable->bar_id = 'Bar';

		$this->assertEquals($expectedTable->getProperties(), $itemTable->getProperties());

		// SET NULL
		$this->instance->getParent()->foo = 5;
		$this->instance->onUpdate(Action::SET_NULL);

		$this->instance->handleUpdateRelations($itemTable);

		$expectedTable->foo_id = null;
		$expectedTable->bar_id = null;

		$this->assertEquals($expectedTable->getProperties(), $itemTable->getProperties());
	}

	/**
	 * Method to test handleDeleteRelations().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::handleDeleteRelations
	 */
	public function testHandleDeleteRelations()
	{
		$itemTable = new StubTableRose;
		$expectedTable = new StubTableRose;

		$this->instance->handleDeleteRelations($itemTable);

		$this->assertTrue($itemTable->_delete);

		$this->instance->onDelete(Action::SET_NULL);

		$this->instance->foo_id = 'Foo';
		$this->instance->bar_id = 'Bar';

		$expectedTable->foo_id = null;
		$expectedTable->bar_id = null;

		$this->instance->handleDeleteRelations($itemTable);

		 $this->assertEquals($expectedTable->getProperties(), $itemTable->getProperties());
	}

	/**
	 * Method to test changed().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::changed
	 */
	public function testChanged()
	{
		$itemTable = new StubTableRose;
		$itemTable->foo_id = 'Foo';
		$itemTable->bar_id = 'Bar';

		$parent = $this->instance->getParent();
		$parent->foo = 'Foo';
		$parent->bar = 'Bar';

		$this->assertFalse($this->instance->changed($itemTable));

		$parent->foo = 'Fo';
		$parent->bar = 'Ba';

		$this->assertTrue($this->instance->changed($itemTable));
	}

	/**
	 * Method to test convertToTable().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::convertToTable
	 */
	public function testConvertToTable()
	{
		// Array
		$table = $this->instance->convertToTable(array('title' => 'bar'));

		$this->assertInstanceOf('Windwalker\Table\Table', $table);

		$this->assertEquals('bar', $table->title);

		// Data
		$table = $this->instance->convertToTable(new Data(array('title' => 'bar')));

		$this->assertInstanceOf('Windwalker\Table\Table', $table);

		$this->assertEquals('bar', $table->title);

		// stdClass
		$table = $this->instance->convertToTable((object) array('title' => 'bar'));

		$this->assertInstanceOf('Windwalker\Table\Table', $table);

		$this->assertEquals('bar', $table->title);

		// Iterator
		$table = $this->instance->convertToTable(new \ArrayIterator(array('title' => 'bar')));

		$this->assertInstanceOf('Windwalker\Table\Table', $table);

		$this->assertEquals('bar', $table->title);

		// Table
		$expected = new StubTableSakura;
		$expected->title = 'bar';

		$table = $this->instance->convertToTable($expected);

		$this->assertSame($expected, $table);

		$this->assertEquals('bar', $table->title);
	}

	/**
	 * Method to test convertToData().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::convertToData
	 */
	public function testConvertToData()
	{
		// Array
		$data = $this->instance->convertToData(array('title' => 'bar'));

		$this->assertInstanceOf('Windwalker\Data\Data', $data);

		$this->assertEquals('bar', $data->title);

		// stdClass
		$data = $this->instance->convertToData((object) array('title' => 'bar'));

		$this->assertInstanceOf('Windwalker\Data\Data', $data);

		$this->assertEquals('bar', $data->title);

		// Iterator
		$data = $this->instance->convertToData(new \ArrayIterator(array('title' => 'bar')));

		$this->assertInstanceOf('Windwalker\Data\Data', $data);

		$this->assertEquals('bar', $data->title);

		// Table
		$table = new StubTableSakura;
		$table->title = 'bar';

		$data = $this->instance->convertToData($table);

		$this->assertInstanceOf('Windwalker\Data\Data', $data);

		$this->assertEquals('bar', $data->title);
	}

	/**
	 * Method to test convertToDataSet().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::convertToDataSet
	 */
	public function testConvertToDataSet()
	{
		$set = array(
			array('title' => 1),
			array('title' => 2),
			array('title' => 3),
			array('title' => 4),
		);

		$dataset = $this->instance->convertToDataSet($set);

		$this->assertInstanceOf('Windwalker\Data\DataSet', $dataset);
		$this->assertInstanceOf('Windwalker\Data\Data', $dataset[0]);

		$this->assertEquals(2, $dataset[1]->title);
	}

	/**
	 * Method to test clearPrimaryKeys().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::clearPrimaryKeys
	 */
	public function testClearPrimaryKeys()
	{
		$table = new Table(static::TABLE_SAKURAS, array('id', 'state'));

		$table->id = 5;
		$table->state = 3;

		$this->instance->clearPrimaryKeys($table);

		$this->assertNull($table->id);
		$this->assertNull($table->state);
	}

	/**
	 * Method to test buildLoadQuery().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::buildLoadQuery
	 */
	public function testBuildLoadQuery()
	{
		$sql = <<<SQL
SELECT *
FROM #__testflower_roses
WHERE `foo_id` = 'Foo' AND `bar_id` = 'Bar'
SQL;

		$this->assertStringSafeEquals($sql, $this->instance->buildLoadQuery());
	}

	/**
	 * Method to test getPrefix().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getPrefix
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::setPrefix
	 */
	public function testGetAndSetPrefix()
	{
		$this->assertEquals('', $this->instance->getPrefix());

		$this->instance->setPrefix('JTable');

		$this->assertEquals('JTable', $this->instance->getPrefix());
	}

	/**
	 * Method to test getParent().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getParent
	 * @TODO   Implement testGetParent().
	 */
	public function testGetParent()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test parent().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::parent
	 * @TODO   Implement testParent().
	 */
	public function testParent()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getTarget().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getTarget
	 * @TODO   Implement testGetTarget().
	 */
	public function testGetTarget()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test targetTable().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::targetTable
	 * @TODO   Implement testTargetTable().
	 */
	public function testTargetTable()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getOnUpdate().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getOnUpdate
	 * @TODO   Implement testGetOnUpdate().
	 */
	public function testGetOnUpdate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test onUpdate().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::onUpdate
	 * @TODO   Implement testOnUpdate().
	 */
	public function testOnUpdate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getOnDelete().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getOnDelete
	 * @TODO   Implement testGetOnDelete().
	 */
	public function testGetOnDelete()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test onDelete().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::onDelete
	 * @TODO   Implement testOnDelete().
	 */
	public function testOnDelete()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getField().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getField
	 * @TODO   Implement testGetField().
	 */
	public function testGetField()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test field().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::field
	 * @TODO   Implement testField().
	 */
	public function testField()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getForeignKeys().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getForeignKeys
	 * @TODO   Implement testGetForeignKeys().
	 */
	public function testGetForeignKeys()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test foreignKeys().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::foreignKeys
	 * @TODO   Implement testForeignKeys().
	 */
	public function testForeignKeys()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getOption().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getOption
	 * @TODO   Implement testGetOption().
	 */
	public function testGetOption()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setOption().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::setOption
	 * @TODO   Implement testSetOption().
	 */
	public function testSetOption()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getOptions().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getOptions
	 * @TODO   Implement testGetOptions().
	 */
	public function testGetOptions()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setOptions().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::setOptions
	 * @TODO   Implement testSetOptions().
	 */
	public function testSetOptions()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getTableName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getTableName
	 * @TODO   Implement testGetTableName().
	 */
	public function testGetTableName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setTableName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::setTableName
	 * @TODO   Implement testSetTableName().
	 */
	public function testSetTableName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getDb().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getDb
	 * @TODO   Implement testGetDb().
	 */
	public function testGetDb()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setDb().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::setDb
	 * @TODO   Implement testSetDb().
	 */
	public function testSetDb()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getFlush().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::getFlush
	 * @TODO   Implement testGetFlush().
	 */
	public function testGetFlush()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test flush().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Relation\Handler\AbstractRelationHandler::flush
	 * @TODO   Implement testFlush().
	 */
	public function testFlush()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
