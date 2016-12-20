<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Helper;

use Windwalker\Model\Helper\AdminListHelper;
use Windwalker\Model\ListModel;

/**
 * Test class of \Windwalker\Model\Helper\AdminListHelper
 *
 * @since 2.1
 */
class AdminListHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
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
	 * Method to test handleFilters().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Helper\AdminListHelper::handleFilters
	 *
	 * @param array $filters
	 * @param array $filterFields
	 * @param array $expected
	 *
	 * @dataProvider handleFiltersProvider
	 *
	 */
	public function testHandleFilters($filters, $filterFields, $expected)
	{
		$this->assertEquals($expected, AdminListHelper::handleFilters($filters, $filterFields));
	}

	/**
	 * handleFiltersProvider
	 *
	 * @return array
	 */
	public function handleFiltersProvider()
	{
		return array(
			array(
				// filters
				array(
					'foo' => 'foo_val',
					'bar' => 'bar_val',
					'baz' => 'baz_val'
				),
				// filterFields
				array('foo', 'bar', 'baz'),
				// expected
				array(
					'foo' => 'foo_val',
					'bar' => 'bar_val',
					'baz' => 'baz_val'
				),
			),
			array(
				// filters
				array(
					'foo' => 'foo_val',
					'bar' => 'bar_val',
					'baz' => 'baz_val'
				),
				// filterFields
				array('foo', 'baz'),
				// expected
				array(
					'foo' => 'foo_val',
					'baz' => 'baz_val'
				),
			),

			// Test if filter has value ''
			array(
				// filters
				array(
					'foo' => '',
					'bar' => 'bar_val',
					'baz' => 'baz_val'
				),
				// filterFields
				array('foo', 'baz'),
				// expected
				array(
					'baz' => 'baz_val'
				),
			)
		);
	}

	/**
	 * Method to test handleSearches().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Helper\AdminListHelper::handleSearches
	 *
	 * @param array $searches
	 * @param array $searchFields
	 * @param array $expected
	 *
	 * @dataProvider handleSearchesProvider
	 */
	public function testHandleSearches($searches, $searchFields, $expected)
	{
		$this->assertEquals($expected, AdminListHelper::handleSearches($searches, $searchFields));
	}

	/*
	 * handleSearchesProvider
	 *
	 * @return array
	 */
	public function handleSearchesProvider() {
		return array(

			// Search with specific field name
			array(
				// searches
				array(
					'field' => 'item.title',
					'index' => 'foo',
				),
				// searchFields
				array(
					'item.title', 'category.title'
				),
				// Expected
				array(
					'item.title' => 'foo',
				),
			),

			// Search all field
			array(
				// searches
				array(
					'field' => '*',
					'index' => 'foo',
				),
				// searchFields
				array(
					'item.title', 'category.title'
				),
				// Expected
				array(
					'item.title' => 'foo',
					'category.title' => 'foo'
				),
			),
		);
	}

	/**
	 * Method to test handleFullordering().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Helper\AdminListHelper::handleFullordering
	 *
	 * @dataProvider handleFullorderingProvider
	 */
	public function testHandleFullordering($value, $orderConfig, $filterFields, $expected)
	{
		$model = new ListModel(array('allow_fields' => $filterFields));

		$this->assertEquals($expected, AdminListHelper::handleFullordering($value, $orderConfig, $model));
	}

	/**
	 * handleFullorderingProvider
	 *
	 * @return array
	 */
	public function handleFullorderingProvider() {
		return array(

			// Ordering with input value
			array(
				// value
				'item.state DESC',
				// orderConfig
				array(
					'ordering'  => 'item.catid, item.ordering',
					'direction' => 'ASC'
				),
				// filterFields
				array(
					'item.id', 'item.catid', 'item.ordering', 'item.state'
				),
				// Expected
				array(
					'ordering'  => 'item.state',
					'direction' => 'DESC'
				),
			),

			// Test with multiple input values
			array(
				// value
				'item.state DESC, item.id DESC',
				// orderConfig
				array(
					'ordering'  => 'item.catid, item.ordering',
					'direction' => 'ASC'
				),
				// filterFields
				array(
					'item.id'
				),
				// Expected
				array(
					'ordering'  => 'item.id',
					'direction' => 'DESC'
				),
			),

			// Test when orderConfig is null
			array(
				// value
				'item.id DESC',
				// orderConfig
				null,
				// filterFields
				array(
					'item.id', 'item.catid', 'item.ordering', 'item.state'
				),
				// Expected
				array(
					'ordering'  => 'item.id',
					'direction' => 'DESC'
				),
			),

			// Ordering with no input value
			array(
				// value
				'',
				// orderConfig
				array(
					'ordering'  => 'item.catid, item.ordering',
					'direction' => 'ASC'
				),
				// filterFields
				array(
					'item.id', 'item.catid', 'item.ordering', 'item.state'
				),
				// Expected
				array(
					'ordering'  => 'item.catid, item.ordering',
					'direction' => 'ASC'
				),
			),

			// Ordering with input value field invalid()
			array(
				// value
				'item.state DESC',
				// orderConfig
				array(
					'ordering'  => 'item.catid, item.ordering',
					'direction' => 'ASC'
				),
				// filterFields
				array(
					'item.id', 'item.catid', 'item.ordering'
				),
				// Expected
				array(
					'ordering'  => 'item.catid, item.ordering',
					'direction' => 'ASC'
				),
			),

		);
	}

}
