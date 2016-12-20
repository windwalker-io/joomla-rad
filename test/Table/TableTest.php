<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Table;

use Windwalker\Table\Table;
use Windwalker\Test\Database\AbstractDatabaseTestCase;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Table\Table
 *
 * @since 2.1
 */
class TableTest extends AbstractDatabaseTestCase
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
	 * @covers \Windwalker\Table\Table::__construct
	 */
	public function test__construct()
	{
		$tableName = '#__test_table';
		$table = new Table($tableName);

		$this->assertEquals($tableName, TestHelper::getValue($table, '_tbl'));
		$this->assertEquals(array('id'), TestHelper::getValue($table, '_tbl_keys'));
		$this->assertSame(\JFactory::getDbo(), $table->getDbo());

		$tableName = '#__test_table2';
		$db = $this->getMockBuilder(get_class(\JFactory::getDbo()))
			->disableOriginalConstructor()->getMock();

		// Just return something to make getFields() no crash.
		$db->expects($this->once())
			->method('getTableColumns')
			->willReturn(array('#__test_table2' => true));

		$table = new Table($tableName, 'pk', $db);

		$this->assertEquals($tableName, TestHelper::getValue($table, '_tbl'));
		$this->assertEquals(array('pk'), TestHelper::getValue($table, '_tbl_keys'));
		$this->assertSame($db, $table->getDbo());
	}

	/**
	 * Method to test store().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Table\Table::store
	 */
	public function testStore()
	{
		$tableName = '#__test_table2';
		$table = new Table($tableName);

		$table->bar = 'foo';
		$table->params = array('foo' => 'bar');
		$table->store();

		$this->assertEquals('{"foo":"bar"}', $table->params);
	}
}
