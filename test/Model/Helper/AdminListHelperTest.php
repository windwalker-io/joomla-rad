<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Helper;

use Windwalker\Model\Helper\AdminListHelper;

/**
 * Test class of \Windwalker\Model\Helper\AdminListHelper
 *
 * @since {DEPLOY_VERSION}
 */
class AdminListHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Model\Helper\AdminListHelper
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
	 * @covers Windwalker\Model\Helper\AdminListHelper::handleFilters
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
				array(
					'foo' => 'foo_val',
					'bar' => 'bar_val',
					'baz' => 'baz_val'
				),
				array('foo', 'bar', 'baz'),
				array(
					'foo' => 'foo_val',
					'bar' => 'bar_val',
					'baz' => 'baz_val'
				),
			),
			array(
				array(
					'foo' => 'foo_val',
					'bar' => 'bar_val',
					'baz' => 'baz_val'
				),
				array('foo', 'baz'),
				array(
					'foo' => 'foo_val',
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
	 * @covers Windwalker\Model\Helper\AdminListHelper::handleSearches
	 *
	 * @param array $searches
	 * @param array $filterFields
	 * @param array $searchFields
	 * @param array $expected
	 *
	 * @dataProvider handleSearchesProvider
	 */
	public function testHandleSearches($searches, $filterFields, $searchFields, $expected)
	{
		$this->assertEquals($expected, AdminListHelper::handleSearches($searches, $filterFields, $searchFields));
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
				// filterFields
				array('item.id', 'item.title', 'item.alias', 'category.title'),
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
				// filterFields
				array('item.id', 'item.title', 'item.alias', 'category.title'),
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
	 * @covers Windwalker\Model\Helper\AdminListHelper::handleFullordering
	 * @TODO   Implement testHandleFullordering().
	 */
	public function testHandleFullordering()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
