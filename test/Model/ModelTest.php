<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model;

require_once __DIR__ . '/Stub/WindwalkerModelStub.php';

use Joomla\Registry\Registry;
use Windwalker\DI\Container;
use Windwalker\Test\Joomla\MockDatabaseDriver;
use Windwalker\Test\Model\Stub\StubModel;
use Windwalker\Test\TestHelper;

/**
 * Test class of Model
 *
 * @since 2.1
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \WindwalkerModelStub
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
		$this->instance = new \WindwalkerModelStub;
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
	 * @covers \Windwalker\Model\Model::__construct
	 */
	public function test__construct()
	{
		// Test for raw class but with naming convention
		$model = new \WindwalkerModelStub;

		// Auto detect model position and name
		$this->assertEquals('windwalker',          TestHelper::getValue($model, 'prefix'));
		$this->assertEquals('com_windwalker',      TestHelper::getValue($model, 'option'));
		$this->assertEquals('stub',                $model->getName());
		$this->assertEquals('com_windwalker.stub', TestHelper::getValue($model, 'context'));

		$this->assertEquals('onContentCleanCache',          TestHelper::getValue($model, 'eventCleanCache'));
		$this->assertInstanceOf('Windwalker\DI\Container',  $model->getContainer());
		$this->assertInstanceOf('Joomla\Registry\Registry', $model->getState());
		$this->assertTrue($model->populateState);

		// Test with config
		$config = array(
			'prefix' => 'flower',
			'name'   => 'sakura',
			'event_clean_cache' => 'onSakuraCleanCache',
			'ignore_request'    => true
		);

		$state = new Registry;
		$db = \JFactory::getDbo();
		$container = new Container;

		$model = new \WindwalkerModelStub($config, $container, $state, $db);

		$this->assertEquals('flower',             TestHelper::getValue($model, 'prefix'));
		$this->assertEquals('com_flower',         TestHelper::getValue($model, 'option'));
		$this->assertEquals('sakura',             $model->getName());
		$this->assertEquals('com_flower.sakura',  TestHelper::getValue($model, 'context'));
		$this->assertEquals('onSakuraCleanCache', TestHelper::getValue($model, 'eventCleanCache'));
		$this->assertSame($container,  $model->getContainer());
		$this->assertSame($state, $model->getState());
		$this->assertSame($db, $model->getDb());
		$this->assertFalse($model->populateState);

		// Test preset in class
		$model = new StubModel;

		$this->assertEquals('windwalker',          TestHelper::getValue($model, 'prefix'));
		$this->assertEquals('com_windwalker',      TestHelper::getValue($model, 'option'));
		$this->assertEquals('stub',                $model->getName());
		$this->assertEquals('com_windwalker.stub', TestHelper::getValue($model, 'context'));
		$this->assertEquals('onTestCleanCache',    TestHelper::getValue($model, 'eventCleanCache'));
	}

	/**
	 * Method to test getName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::getName
	 */
	public function testGetName()
	{
		$this->assertEquals('stub', $this->instance->getName());
	}

	/**
	 * Method to test getTable().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::getTable
	 */
	public function testGetTable()
	{
		$this->assertInstanceOf('JTableContent', $this->instance->getTable('Content', 'JTable'));
	}

	/**
	 * Method to test registerTablePaths().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::registerTablePaths
	 */
	public function testRegisterTablePaths()
	{
		$model = new \WindwalkerModelStub(array('table_path' => '/foo/bar'));

		$this->assertTrue(in_array('/foo/bar', $model::$paths));
	}

	/**
	 * Method to test setName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::setName
	 */
	public function testSetName()
	{
		$this->instance->setName('flower');

		$this->assertEquals('flower', $this->instance->getName());
	}

	/**
	 * Method to test setOption().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::setOption
	 */
	public function testSetOption()
	{
		$this->instance->setOption('com_flower');

		$this->assertEquals('com_flower', TestHelper::getValue($this->instance, 'option'));
	}

	/**
	 * Method to test addTablePath().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::addTablePath
	 */
	public function testAddTablePath()
	{
		$this->instance->addTablePath('/flower/sakura');

		$model = $this->instance;

		$this->assertTrue(in_array('/flower/sakura', $model::$paths));
	}

	/**
	 * Method to test getContainer().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::getContainer
	 * @covers \Windwalker\Model\Model::setContainer
	 */
	public function testGetAndSetContainer()
	{
		$container = new Container;

		$this->instance->setContainer($container);

		$this->assertSame($container, $this->instance->getContainer());
	}

	/**
	 * Method to test get().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::get
	 */
	public function testGetAndSet()
	{
		$this->instance->set('pk', 5);

		$item = $this->instance->getItem();

		$this->assertEquals(5, $item->pk);

		$this->assertEquals(5, $this->instance->get('pk'));
		$this->assertEquals(6, $this->instance->get('none', 6));
	}

	/**
	 * Method to test reset().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::reset
	 */
	public function testReset()
	{
		$this->instance->set('pk', 5);

		$this->instance->reset();

		$this->assertNull($this->instance->get('pk'));
	}

	/**
	 * Method to test offsetExists().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::offsetExists
	 */
	public function testOffsetExists()
	{
		$this->instance->set('item.id', 5);

		$this->assertTrue(isset($this->instance['item.id']));
	}

	/**
	 * Method to test offsetGet().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::offsetGet
	 * @covers \Windwalker\Model\Model::offsetSet
	 */
	public function testOffsetGetAndSet()
	{
		$this->instance['item.id'] = 8;

		$this->assertEquals(8, $this->instance['item.id']);
	}

	/**
	 * Method to test offsetUnset().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::offsetUnset
	 */
	public function testOffsetUnset()
	{
		$this->instance['item.id'] = 9;

		unset($this->instance['item.id']);

		$this->assertNull($this->instance->get('item.id'));
	}

	/**
	 * Method to test getDb().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Model::getDb
	 */
	public function testGetAndSetDb()
	{
		$db = new MockDatabaseDriver;

		$this->instance->setDb($db);

		$this->assertSame($db, $this->instance->getDb());
	}

	/**
	 * testGetStoreId
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Model\Model::getStoreId
	 */
	public function testGetStoreId()
	{
		TestHelper::setValue($this->instance, 'context', 'com_test.item');

		$this->assertEquals(
			md5('com_test.item:' . 123 . ':' . json_encode($this->instance->getState()->toArray())),
			TestHelper::invoke($this->instance, 'getStoreId', 123)
		);
	}

	/**
	 * getGetAndSetCache
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Model\Model::getCache
	 * @covers \Windwalker\Model\Model::setCache
	 */
	public function getGetAndSetCache()
	{
		$item = array('id' => 213);

		TestHelper::invoke($this->instance, 'setCache', 'test.item', $item);

		$this->assertEquals(
			$item,
			TestHelper::invoke($this->instance, 'getCache', 'test.item')
		);
	}

	/**
	 * testHasCache
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Model\Model::hasCache
	 */
	public function testHasCache()
	{
		$this->assertFalse(TestHelper::invoke($this->instance, 'hasCache', 'test.item'));

		$item = array('id' => 213);

		TestHelper::invoke($this->instance, 'setCache', 'test.item', $item);

		$this->assertTrue(TestHelper::invoke($this->instance, 'hasCache', 'test.item'));
	}

	/**
	 * testResetCache
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Model\Model::resetCache
	 * @covers \Windwalker\Model\Model::getCacheObject
	 */
	public function testResetCache()
	{
		$cache = $this->instance->getCacheObject();

		$cache->set('test.item', 123);

		$this->instance->resetCache();

		$this->assertNotSame($cache, $this->instance->getCacheObject());

		$this->assertNull($this->instance->getCacheObject()->get('test.item'));
	}

	/**
	 * testFetch
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Model\Model::fetch
	 */
	public function testFetch()
	{
		$closure = function()
		{
			return array('id' => 213);
		};

		$item = TestHelper::invoke($this->instance, 'fetch', 'test.item', $closure);

		$this->assertEquals(
			$item,
			TestHelper::invoke($this->instance, 'getCache', 'test.item')
		);
	}
}
