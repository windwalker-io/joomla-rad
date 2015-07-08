<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Table;

use Windwalker\Table\Table;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Table\Table
 *
 * @since {DEPLOY_VERSION}
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$sqls = file_get_contents(__DIR__ . '/sql/install.sql');

		foreach (explode(';', $sqls) as $sql)
		{
			$sql = trim($sql);

			if (!empty($sql))
			{
				\JFactory::getDbo()->setQuery($sql)->execute();
			}
		}
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$sql = file_get_contents(__DIR__ . '/sql/uninstall.sql');

		\JFactory::getDbo()->setQuery($sql)->execute();
	}
	
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Table\Table::__construct
	 */
	public function test__construct()
	{
		$tableName = '#__test_table';
		$table = new Table($tableName);

		$this->assertEquals($tableName, TestHelper::getValue($table, '_tbl'));
		$this->assertEquals(array('id'), TestHelper::getValue($table, '_tbl_keys'));
		$this->assertSame(\JFactory::getDbo(), $table->getDbo());

		$tableName = '#__test_table2';
		$db = $this->getMockBuilder('JDatabaseDriver')->disableOriginalConstructor();
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
	 * @covers Windwalker\Table\Table::store
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
