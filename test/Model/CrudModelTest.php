<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model;

use Windwalker\Model\CrudModel;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Model\CrudModel
 *
 * @since 2.1
 */
class CrudModelTest extends AbstractDatabaseTestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Model\CrudModel
	 */
	protected $instance;

	/**
	 * setUpBeforeClass
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		\JTable::addIncludePath(__DIR__ . '/Stub/Table');
	}

	/**
	 * getInstallSql
	 *
	 * @return  string
	 */
	public static function getInstallSql()
	{
		return __DIR__ . '/sql/install.crudmodel.sql';
	}

	/**
	 * getUninstallSql
	 *
	 * @return  string
	 */
	public static function getUninstallSql()
	{
		return __DIR__ . '/sql/uninstall.crudmodel.sql';
	}

	/**
	 * getConstructContainer
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getConstructContainer()
	{
		// The 'testKeyName' will be return by StubTableCrudModel
		$input = new \JInput(array('id' => 'foo'));

		$container = $this->getMockBuilder('Windwalker\DI\Container')
			->disableOriginalConstructor()
			->setMethods(array('get'))
			->getMock();

		$container->expects($this->any())
			->method('get')
			->with('input')
			->will($this->returnValue($input));

		return $container;
	}

	/**
	 * getContainerForSave
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getContainerForSave()
	{
		$container = $this->getMockBuilder('Windwalker\DI\Container')
			->disableOriginalConstructor()
			->setMethods(array('get'))
			->getMock();

		$dispatcher = $this->getMockBuilder('\JEventDispatcher')
			->setMethods(array('trigger'))
			->getMock();

		$dispatcher->expects($this->any())
			->method('trigger')
			->will($this->returnValue(array('true')));

		$container->expects($this->at(0))
			->method('get')
			->with('event.dispatcher')
			->will($this->returnValue($dispatcher));

		// From call point 1~3, CrudModel::cleanCache() is being call
		$container->expects($this->at(1))
			->method('get')
			->with('joomla.config')
			->will($this->returnValue(new \JRegistry));

		$container->expects($this->at(2))
			->method('get')
			->with('event.dispatcher')
			->will($this->returnValue($dispatcher));

		$container->expects($this->at(3))
			->method('get')
			->with('input')
			->will($this->returnValue(new \JInput));

		return $container;
	}

	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\CrudModel::__construct
	 */
	public function test__construct()
	{
		$testState = new \JRegistry;

		$testState->set('CrudModel.id', 'id');

		$crudModel = new CrudModel(
			array(
				'name'   => 'CrudModel',
				'prefix' => 'Stub'
			),
			$this->getConstructContainer(),
			$testState
		);

		$this->assertEquals('onContentAfterDelete', TestHelper::getValue($crudModel, 'eventAfterDelete'));
		$this->assertEquals('onContentBeforeDelete', TestHelper::getValue($crudModel, 'eventBeforeDelete'));
		$this->assertEquals('onContentAfterSave', TestHelper::getValue($crudModel, 'eventAfterSave'));
		$this->assertEquals('onContentAfterSave', TestHelper::getValue($crudModel, 'eventBeforeSave'));
		$this->assertEquals('onContentAfterSave', TestHelper::getValue($crudModel, 'eventChangeState'));

		// Test if PopulateState() was being executed
		// 'foo' is being set in getConstructContainer()
		$this->assertEquals('foo', TestHelper::getValue($crudModel, 'state')->get('CrudModel.id'));
		$this->assertEquals(\JComponentHelper::getParams('com_stub'), TestHelper::getValue($crudModel, 'state')->get('params'));
	}

	/**
	 * Method to test __construct() with 'ignore_request' set to true
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Model\CrudModel::__construct
	 */
	public function test__constructWithIgnoreRequest()
	{
		$testState = new \JRegistry;

		$testState->set('CrudModel.id', 'id');

		$crudModel = new CrudModel(
			array(
				'name'           => 'CrudModel',
				'prefix'         => 'Stub',
				'ignore_request' => true
			),
			$this->getConstructContainer(),
			$testState
		);

		// Since we ignore the request input get from our stub container, the 'CrudModel.id' will remain unchanged
		$this->assertEquals('id', TestHelper::getValue($crudModel, 'state')->get('CrudModel.id'));
	}

	/**
	 * Method to test getItem().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\CrudModel::getItem
	 */
	public function testGetItem()
	{
		$crudModel = new CrudModel(
			array(
				'name'           => 'CrudModel',
				'prefix'         => 'Stub',
				'ignore_request' => true
			),
			$this->getConstructContainer()
		);

		$expected = (object) array(
			'id'     => '1',
			'foo'    => 'bad',
			'type'   => 'fruit',
			'params' => Array(
				'name' => 'apple'
			)
		);

		$this->assertEquals($expected, $crudModel->getItem(1));
	}

	/**
	 * Method to test save().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\CrudModel::save
	 */
	public function testSaveWithId()
	{
		$crudModel = new CrudModel(
			array(
				'name'           => 'CrudModel',
				'prefix'         => 'Stub',
				'ignore_request' => true
			),
			$this->getContainerForSave()
		);

		$toSaveWithId = array(
			'id'     => '1',
			'foo'    => 'badBoy',
			'type'   => 'fruit',
			'params' => Array(
				'name' => 'apple'
			)
		);

		$crudModel->save($toSaveWithId);

		$this->assertEquals((object) $toSaveWithId, $crudModel->getItem(1));
	}

	/**
	 * Method to test save().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\CrudModel::save
	 */
	public function testSaveWithoutId()
	{
		$crudModel = new CrudModel(
			array(
				'name'           => 'CrudModel',
				'prefix'         => 'Stub',
				'ignore_request' => true
			),
			$this->getContainerForSave()
		);

		$toSaveWithoutId = array(
			'foo'    => 'badGirl',
			'type'   => 'badGirlType',
			'params' => Array(
				'name' => 'badGirlName'
			)
		);

		$crudModel->save($toSaveWithoutId);

		$item = $crudModel->getItem();

		$this->assertEquals($toSaveWithoutId['foo'], $item->foo);
		$this->assertEquals($toSaveWithoutId['type'], $item->type);
		$this->assertEquals($toSaveWithoutId['params'], $item->params);
	}

	/**
	 * Method to test updateState().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\CrudModel::updateState
	 */
	public function testUpdateState()
	{
		$crudModel = new CrudModel(
			array(
				'name'           => 'CrudModel',
				'prefix'         => 'Stub',
				'ignore_request' => true
			),
			$this->getContainerForSave()
		);

		$originalData = $crudModel->getItem(1);

		$toUpdate = array(
			'foo'    => 'Star Wars',
			'type'   => 'Soldier',
		);

		// Execute method
		$crudModel->updateState($originalData->id, $toUpdate);

		$result = $crudModel->getItem($originalData->id);

		// Test changed fields
		$this->assertEquals($toUpdate['foo'], $result->foo);
		$this->assertEquals($toUpdate['type'], $result->type);

		// Test unchanged fields
		$this->assertEquals($originalData->params, $result->params);
	}

	/**
	 * Method to test delete().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\CrudModel::delete
	 */
	public function testDelete()
	{
		$crudModel = new CrudModel(
			array(
				'name'           => 'CrudModel',
				'prefix'         => 'Stub',
				'ignore_request' => true
			),
			$this->getContainerForSave()
		);

		$id = 1;
		$originalData = $crudModel->getItem($id);

		// Execute method
		$crudModel->delete($id);

		// $id is pass by reference which will be cast into array
		$result = $crudModel->getItem($id[0]);

		$this->assertNull($result->id);
		$this->assertNull($result->foo);
		$this->assertNull($result->type);
		$this->assertEmpty($result->params);
	}

}
