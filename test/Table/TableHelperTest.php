<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Table;

use Windwalker\Table\TableHelper;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Table\TableHelper
 *
 * @since 2.1
 */
class TableHelperTest extends AbstractDatabaseTestCase
{
	/**
	 * Install test sql when setUp.
	 *
	 * @return  string
	 */
	public static function getInstallSql()
	{
		return __DIR__ . '/sql/install.sql';
	}

	/**
	 * Uninstall test sql when tearDown.
	 *
	 * @return  string
	 */
	public static function getUninstallSql()
	{
		return __DIR__ . '/sql/uninstall.sql';
	}
	
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Table\TableHelper::__construct
	 */
	public function test__construct()
	{
		$tableName = '#__test_table';
		$helper = new TableHelper($tableName);

		$this->assertEquals($tableName, TestHelper::getValue($helper, 'table'));
		$this->assertEquals('id', TestHelper::getValue($helper, 'pkName'));
		$this->assertSame(\JFactory::getDbo(), $helper->getDb());

		$tableName = '#__test_table2';
		$helper = new TableHelper($tableName, null, 'pk');

		$this->assertEquals($tableName, TestHelper::getValue($helper, 'table'));
		$this->assertEquals('pk', TestHelper::getValue($helper, 'pkName'));
		$this->assertSame(\JFactory::getDbo(), $helper->getDb());
	}

	/**
	 * Method to test exists().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Table\TableHelper::exists
	 */
	public function testExists()
	{
		$helper = new TableHelper('#__test_table');

		$this->assertTrue($helper->exists(1));
		$this->assertTrue($helper->exists(2));

		$helper = new TableHelper('#__test_table2', null, 'pk');

		$this->assertTrue($helper->exists(1));
		$this->assertTrue($helper->exists(2));
		$this->assertTrue($helper->exists(3));
	}

	/**
	 * Method to test initRow().
	 *
	 * @param string $tableName
	 * @param string $pk
	 * @param int    $initId
	 * @param int    $expectCount
	 *
	 * @covers       Windwalker\Table\TableHelper::initRow
	 * @dataProvider initRowDataProvider
	 */
	public function testInitRow($tableName, $pk, $initId, $expectCount)
	{
		$helper = new TableHelper($tableName, null, $pk);

		$db = \JFactory::getDbo();
		$idQuery = $db->getQuery(true);
		$countQuery = $db->getQuery(true);

		$idQuery->select($pk)->from($tableName)->where("$pk = $initId");
		$countQuery->select('COUNT(*)')->from($tableName);

		$this->assertEquals($initId, $helper->initRow($initId));

		$actualId = $db->setQuery($idQuery)->loadResult();
		$actualCount = $db->setQuery($countQuery)->loadResult();

		$this->assertEquals($initId, $actualId, 'Check init new id');
		$this->assertEquals($expectCount, $actualCount, 'Check row count after init new id');
	}

	/**
	 * initRowDataProvider
	 *
	 * @return  array
	 */
	public function initRowDataProvider()
	{
		return array(
			//    tableName,        pk,   initId, expectCount
			array('#__test_table',  'id', 1,      2),
			array('#__test_table',  'id', 100,    3),
			array('#__test_table2', 'pk', 3,      3),
			array('#__test_table2', 'pk', 5,      4),
		);
	}
}
