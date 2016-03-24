<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model;

use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\Model\Stub\WindwalkerModelStubItem;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Model\ItemModel
 *
 * @since 2.1
 */
class ItemModelTest extends AbstractDatabaseTestCase
{
	/**
	 * setUpBeforeClass
	 *
	 * @throws  \LogicException
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		\JTable::addIncludePath(__DIR__ . '/Stub/Table');

		parent::setUpBeforeClass();
	}

	/**
	 * Install test sql when setUp.
	 *
	 * @return  string
	 */
	public static function getInstallSql()
	{
		return __DIR__ . '/sql/install.itemmodel.sql';
	}

	/**
	 * Uninstall test sql when tearDown.
	 *
	 * @return  string
	 */
	public static function getUninstallSql()
	{
		return __DIR__ . '/sql/install.itemmodel.sql';
	}

	/**
	 * getConstructContainer
	 *
	 * @param   mixed  $key
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getConstructContainer($key)
	{
		$input = new \JInput(array('id' => $key));

		$container = $this->getMockBuilder('Windwalker\DI\Container')
			->disableOriginalConstructor()
			->setMethods(array('get'))
			->getMock();

		$container->expects($this->once())
			->method('get')
			->with('input')
			->will($this->returnValue($input));

		return $container;
	}

	/**
	 * test__construct
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Model\ItemModel::__construct
	 * @group   __construct
	 */
	public function test__construct()
	{
		$itemModel = new WindwalkerModelStubItem;

		$prefix = TestHelper::getValue($itemModel, 'prefix');
		$name = TestHelper::getValue($itemModel, 'name');
		$option = TestHelper::getValue($itemModel, 'option');
		$context = TestHelper::getValue($itemModel, 'context');
		$eventCleanCache = TestHelper::getValue($itemModel, 'eventCleanCache');

		$this->assertEquals('windwalker', $prefix);
		$this->assertEquals('stubitem', $name);
		$this->assertEquals('com_windwalker', $option);
		$this->assertEquals('com_windwalker.stubitem', $context);
		$this->assertEquals('onTestCleanCache', $eventCleanCache);
		$this->assertNull($itemModel->get('ignore_request'));
	}

	/**
	 * test__constructIgnoreRequest
	 *
	 * @return  void
	 *
	 * @covers  \Windwalker\Model\ItemModel::__construct
	 * @group   __constructIgnoreRequest
	 */
	public function test__constructIgnoreRequest()
	{
		$config['ignore_request'] = true;

		$itemModel = new WindwalkerModelStubItem($config);

		$prefix = TestHelper::getValue($itemModel, 'prefix');
		$name = TestHelper::getValue($itemModel, 'name');
		$option = TestHelper::getValue($itemModel, 'option');
		$context = TestHelper::getValue($itemModel, 'context');
		$eventCleanCache = TestHelper::getValue($itemModel, 'eventCleanCache');

		$this->assertEquals('windwalker', $prefix);
		$this->assertEquals('stubitem', $name);
		$this->assertEquals('com_windwalker', $option);
		$this->assertEquals('com_windwalker.stubitem', $context);
		$this->assertEquals('onTestCleanCache', $eventCleanCache);
		$this->assertTrue($itemModel->get('ignore_request'));
	}

	/**
	 * testGetItem
	 *
	 * @param   mixed  $pk
	 * @param   array  $expected
	 *
	 * @return  void
	 *
	 * @depends  test__construct
	 *
	 * @dataProvider  getItemDataProvider
	 * @covers        \Windwalker\Model\ItemModel::getItem
	 * @group         getItem
	 */
	public function testGetItem($pk, $expected)
	{
		$itemModel = new WindwalkerModelStubItem(
			array(),
			$this->getConstructContainer($pk)
		);

		$item = $itemModel->getItem($pk);

		$this->assertNull($itemModel->get('ignore_request'));
		$this->assertEquals($pk, $itemModel->get($itemModel->getName() . '.id'));
		$this->assertEquals((object) $expected, $item);
		$this->assertEquals($expected['params'], $item->params);
	}

	/**
	 * testGetItemIgnoreRequest
	 *
	 * @param   mixed  $pk
	 * @param   array  $expected
	 *
	 * @return  void
	 *
	 * @depends  test__constructIgnoreRequest
	 *
	 * @dataProvider  getItemDataProvider
	 * @covers        \Windwalker\Model\ItemModel::getItem
	 * @group         getItemIgnoreRequest
	 */
	public function testGetItemIgnoreRequest($pk, $expected)
	{
		$config['ignore_request'] = true;

		$itemModel = new WindwalkerModelStubItem($config);

		$item = $itemModel->getItem($pk);

		$this->assertTrue($itemModel->get('ignore_request'));
		$this->assertNull($itemModel->get($itemModel->getName() . '.id'));
		$this->assertEquals((object) $expected, $item);
		$this->assertEquals($expected['params'], $item->params);
	}

	/**
	 * testGetItemDataProvider
	 *
	 * @return  array
	 */
	public function getItemDataProvider()
	{
		$data = array();

		// The primary key is null
		$data[0][0] = null;
		$data[0][1] = array(
			'id' => null,
			'foo' => null,
			'type' => null,
			'params' => array()
		);

		$data[1][0] = 1;
		$data[1][1] = array(
			'id' => '1',
			'foo' => 'bad',
			'type' => 'fruit',
			'params' => array('name' => 'apple')
		);

		$data[2][0] = 6;
		$data[2][1] = array(
			'id' => '6',
			'foo' => 'bam',
			'type' => 'flower',
			'params' => array('name' => 'sakura')
		);

		$data[3][0] = 9;
		$data[3][1] = array(
			'id' => '9',
			'foo' => 'bat',
			'type' => 'fruit',
			'params' => array('name' => 'strawberry')
		);

		// The primary key is not exists
		$data[4][0] = 20;
		$data[4][1] = array(
			'id' => null,
			'foo' => null,
			'type' => null,
			'params' => array()
		);

		return $data;
	}
}
