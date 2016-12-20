<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Filter;

use \Windwalker\Model\Filter\SearchHelper;
use Windwalker\Test\TestCase\AbstractBaseTestCase;

/**
 * Test class of \Windwalker\Model\Filter\SearchHelper
 *
 * @since 2.1
 */
class SearchHelperTest extends AbstractBaseTestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Model\Filter\SearchHelper
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
		$this->instance = new SearchHelper;
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
	 * @param  array $filters
	 * @param  array $expected
	 *
	 * @return void
	 *
	 * @dataProvider searchesProvider
	 * @covers \Windwalker\Model\Filter\SearchHelper::execute
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
	 * @param callback $handler
	 * @param array    $filter
	 * @param string   $key
	 * @param string   $expected
	 *
	 * @return  void
	 *
	 * @dataProvider handlerProvider
	 * @covers \Windwalker\Model\Filter\SearchHelper::execute
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
	 * searchesProvider
	 *
	 * @return  array
	 */
	public function searchesProvider()
	{
		return array(
			array(
				array('name' => 'apple'),
				"SELECT *\nFROM table\nWHERE \n(`name` LIKE '%apple%')",
			),
			array(
				array('name' => 'book', 'info' => 'people'),
				"SELECT *\nFROM table\nWHERE \n(`name` LIKE '%book%' \nOR `info` LIKE '%people%')",
			),
			array(
				array('info' => 'second', 'address' => 'taipei'),
				"SELECT *\nFROM table\nWHERE \n(`info` LIKE '%second%' \nOR `address` LIKE '%taipei%')",
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
			$query->where($field . ' NOT LIKE ' . $value);
		};

		$handler2 = function(\JDatabaseQuery $query, $field, $value)
		{
			$query->where($field . ' DO LIKE ' . $value);
		};

		$handler3 = function(\JDatabaseQuery $query, $field, $value)
		{
			$query->where($field . ' IS LIKE ' . $value);
		};

		return array(
			array(
				$handler1,
				array('john' => 'marry'),
				'john',
				"SELECT *\nFROM table\nWHERE john NOT LIKE marry",
			),
			array(
				$handler2,
				array('boy' => 'girl'),
				'boy',
				"SELECT *\nFROM table\nWHERE boy DO LIKE girl",
			),
			array(
				$handler3,
				array('she' => 'him'),
				'she',
				"SELECT *\nFROM table\nWHERE she IS LIKE him"
			),
		);
	}
}
