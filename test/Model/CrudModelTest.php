<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model;

use Windwalker\Model\CrudModel;
use Windwalker\Test\TestHelper;

require_once(__DIR__ . '/Stub/Table/StubTableCrudModel.php');

/**
 * Test class of \Windwalker\Model\CrudModel
 *
 * @since {DEPLOY_VERSION}
 */
class CrudModelTest extends \PHPUnit_Framework_TestCase
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
		$db = \JFactory::getDbo();
		$sqls = file_get_contents(__DIR__ . '/sql/install.crudmodel.sql');

		foreach ($db->splitSql($sqls) as $sql)
		{
			$sql = trim($sql);
			if (!empty($sql))
			{
				$db->setQuery($sql)->execute();
			}
		}
	}
	/**
	 * tearDownAfterClass
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		$sql = file_get_contents(__DIR__ . '/sql/uninstall.crudmodel.sql');

		\JFactory::getDbo()->setQuery($sql)->execute();
	}
	
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::__construct
	 */
	public function test__construct()
	{
		$crudModel = new CrudModel(
			array(
				'name' => 'CrudModel',
				'prefix' => 'Stub'
			)
		);

		$this->assertEquals('onContentAfterDelete', TestHelper::getValue($crudModel, 'eventAfterDelete'));
		$this->assertEquals('onContentBeforeDelete', TestHelper::getValue($crudModel, 'eventBeforeDelete'));
		$this->assertEquals('onContentAfterSave', TestHelper::getValue($crudModel, 'eventAfterSave'));
		$this->assertEquals('onContentAfterSave', TestHelper::getValue($crudModel, 'eventBeforeSave'));
		$this->assertEquals('onContentAfterSave', TestHelper::getValue($crudModel, 'eventChangeState'));
	}

	/**
	 * Method to test getItem().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getItem
	 * @TODO   Implement testGetItem().
	 */
	public function testGetItem()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test save().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::save
	 * @TODO   Implement testSave().
	 */
	public function testSave()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test postSaveHook().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::postSaveHook
	 * @TODO   Implement testPostSaveHook().
	 */
	public function testPostSaveHook()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test updateState().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::updateState
	 * @TODO   Implement testUpdateState().
	 */
	public function testUpdateState()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test delete().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::delete
	 * @TODO   Implement testDelete().
	 */
	public function testDelete()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getForm().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getForm
	 * @TODO   Implement testGetForm().
	 */
	public function testGetForm()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test validate().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::validate
	 * @TODO   Implement testValidate().
	 */
	public function testValidate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getParams().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getParams
	 * @TODO   Implement testGetParams().
	 */
	public function testGetParams()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getCategory().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getCategory
	 * @TODO   Implement testGetCategory().
	 */
	public function testGetCategory()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getName().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getName
	 * @TODO   Implement testGetName().
	 */
	public function testGetName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getTable().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getTable
	 * @TODO   Implement testGetTable().
	 */
	public function testGetTable()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test registerTablePaths().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::registerTablePaths
	 * @TODO   Implement testRegisterTablePaths().
	 */
	public function testRegisterTablePaths()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setName().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::setName
	 * @TODO   Implement testSetName().
	 */
	public function testSetName()
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
	 * @covers Windwalker\Model\CrudModel::setOption
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
	 * Method to test addTablePath().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::addTablePath
	 * @TODO   Implement testAddTablePath().
	 */
	public function testAddTablePath()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getContainer().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getContainer
	 * @TODO   Implement testGetContainer().
	 */
	public function testGetContainer()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setContainer().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::setContainer
	 * @TODO   Implement testSetContainer().
	 */
	public function testSetContainer()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test get().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::get
	 * @TODO   Implement testGet().
	 */
	public function testGet()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test set().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::set
	 * @TODO   Implement testSet().
	 */
	public function testSet()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test reset().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::reset
	 * @TODO   Implement testReset().
	 */
	public function testReset()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test offsetExists().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::offsetExists
	 * @TODO   Implement testOffsetExists().
	 */
	public function testOffsetExists()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test offsetGet().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::offsetGet
	 * @TODO   Implement testOffsetGet().
	 */
	public function testOffsetGet()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test offsetSet().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::offsetSet
	 * @TODO   Implement testOffsetSet().
	 */
	public function testOffsetSet()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test offsetUnset().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::offsetUnset
	 * @TODO   Implement testOffsetUnset().
	 */
	public function testOffsetUnset()
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
	 * @covers Windwalker\Model\CrudModel::getDb
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
	 * @covers Windwalker\Model\CrudModel::setDb
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
	 * Method to test getState().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::getState
	 * @TODO   Implement testGetState().
	 */
	public function testGetState()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setState().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\CrudModel::setState
	 * @TODO   Implement testSetState().
	 */
	public function testSetState()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
