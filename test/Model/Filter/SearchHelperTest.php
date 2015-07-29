<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Filter;

use \Windwalker\Model\Filter\SearchHelper;

/**
 * Test class of \Windwalker\Model\Filter\SearchHelper
 *
 * @since {DEPLOY_VERSION}
 */
class SearchHelperTest extends \PHPUnit_Framework_TestCase
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
	 * @param  array $searches
	 * @param  array $expected
	 *
	 * @return void
	 *
	 * @dataProvider searchesProvider
	 * @covers Windwalker\Model\Filter\SearchHelper::execute
	 */
	public function testExecute($searches, $expected)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);

		$query = $this->instance->execute($query, $searches);

		$readWhere = $this->readAttribute($query, 'where');

		$readElements = $this->readAttribute($readWhere, 'elements');

		$readElements = $this->readAttribute($readElements[0], 'elements');

		$this->assertSame($expected, $readElements);
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
				array("`name` LIKE '%apple%'"),
			),
			array(
				array('name' => 'book', 'info' => 'people'),
				array("`name` LIKE '%book%'", "`info` LIKE '%people%'"),
			),
			array(
				array('info' => 'second', 'address' => 'taipei'),
				array("`info` LIKE '%second%'", "`address` LIKE '%taipei%'"),
			),
		);
	}
}
