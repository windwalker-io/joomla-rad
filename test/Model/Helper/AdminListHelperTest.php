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
	 * @TODO   Implement testHandleSearches().
	 */
	public function testHandleSearches()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
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
