<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Filter;

use \Windwalker\Model\Filter\FilterHelper;

/**
 * Test class of \Windwalker\Model\Filter\FilterHelper
 *
 * @since {DEPLOY_VERSION}
 */
class FilterHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Model\Filter\FilterHelper
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
		$this->instance = new FilterHelper;
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
	 * Method to test execute().
	 *
	 * @param array $fields
	 * @param array $expected
	 *
	 * @return void
	 *
	 * @dataProvider fieldsProvider
	 * @covers Windwalker\Model\Filter\FilterHelper::execute
	 */
	public function testExecute($fields, $expected)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query = $this->instance->execute($query, $fields);

		$readWhere = $this->readAttribute($query, 'where');

		$readElements = $this->readAttribute($readWhere, 'elements');

		$this->assertSame($expected, $readElements);
	}

	/**
	 * fieldsProvider
	 *
	 * @return  array
	 */
	public function fieldsProvider()
	{
		return array(
			array(
				array('id' => '1', 'name' => 'apple'),
				array("`id` = '1'", "`name` = 'apple'"),
			),
			array(
				array('number' => '2', 'name' => 'book'),
				array("`number` = '2'", "`name` = 'book'"),
			),
			array(
				array('name' => 'car', 'id' => '3'),
				array("`name` = 'car'", "`id` = '3'"),
			),
		);
	}
}
