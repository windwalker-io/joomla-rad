<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Filter;

use \Windwalker\Model\Filter\FilterHelper;
use \Windwalker\Test\TestCase\AbstractBaseTestCase;

/**
 * Test class of \Windwalker\Model\Filter\FilterHelper
 *
 * @since 2.1
 */
class FilterHelperTest extends AbstractBaseTestCase
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
	 * @param array $filters
	 * @param array $expected
	 *
	 * @return void
	 *
	 * @dataProvider fieldsProvider
	 * @covers \Windwalker\Model\Filter\FilterHelper::execute
	 */
	public function testExecute($filters, $expected)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')->from('table');

		$query = $this->instance->execute($query, $filters);

		$this->assertStringSafeEquals($expected, (string) $query);
	}

	/**
	 * testHandler
	 *
	 * @param  callback $handler
	 * @param  array    $filter
	 * @param  string   $key
	 * @param  string   $expected
	 *
	 * @return  void
	 *
	 * @dataProvider handlerProvider
	 * @covers \Windwalker\Model\Filter\FilterHelper::execute
	 */
	public function testHandler($handler, $filter, $key, $expected)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')->from('table');

		$this->instance->setHandler($key, $handler);

		$query = $this->instance->execute($query, $filter);

		$this->assertStringSafeEquals($expected, (string) $query);
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
				"SELECT *\nFROM table\nWHERE `id` = '1' AND `name` = 'apple'",
			),
			array(
				array('number' => '2', 'name' => 'book'),
				"SELECT *\nFROM table\nWHERE `number` = '2' AND `name` = 'book'",
			),
			array(
				array('name' => 'car', 'id' => '3'),
				"SELECT *\nFROM table\nWHERE `name` = 'car' AND `id` = '3'",
			),
		);
	}

	/**
	 * handlerProvider
	 *
	 * @return  array
	 */
	public function handlerProvider()
	{
		$handler1 = function(\JDatabaseQuery $query, $field, $value)
		{
			$query->where($field . ' != ' . $value);
		};

		$handler2 = function(\JDatabaseQuery $query, $field, $value)
		{
			$query->where($query->quoteName($field) . ' >= ' . $query->quoteName($value));
		};

		$handler3 = function(\JDatabaseQuery $query, $field, $value)
		{
			$query->where($field . ' <= ' . $value);
		};

		return array(
			array(
				$handler1,
				array('id' => '1'),
				'id',
				"SELECT *\nFROM table\nWHERE id != 1",
			),
			array(
				$handler2,
				array('name' => 'apple'),
				'name',
				"SELECT *\nFROM table\nWHERE `name` >= `apple`",
			),
			array(
				$handler3,
				array('number' => '287578'),
				'number',
				"SELECT *\nFROM table\nWHERE number <= 287578"
			),
		);
	}
}
