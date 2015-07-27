<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Filter;

/**
 * Test class of \Windwalker\Model\Filter\AbstractFilterHelper
 *
 * @since {DEPLOY_VERSION}
 */
class AbstractFilterHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Model\Filter\AbstractFilterHelper
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
		$this->instance = $this->getMockForAbstractClass('Windwalker\Model\Filter\AbstractFilterHelper');
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
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\Filter\AbstractFilterHelper::__construct
	 */
	public function test__construct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setHandler().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\Filter\AbstractFilterHelper::setHandler
	 */
	public function testSetHandler()
	{
		$handler = function ($arg1, $arg2, $arg5){
			return array($arg1, $arg2, $arg5);
		};

		$name = 'myHandler';

		$setHandler = $this->instance->setHandler($name, $handler);

		$getHandler = $this->getObjectAttribute($setHandler, 'handler');

		$this->assertArrayHasKey($name, $getHandler);
	}

	/**
	 * Method to test setDefaultHandler().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Model\Filter\AbstractFilterHelper::setDefaultHandler
	 */
	public function testSetDefaultHandler()
	{
		$defaultHandler = function ($arg1, $arg2, $arg5){
			return array($arg1, $arg2, $arg5);
		};

		$setDefaultHandler = $this->instance->setDefaultHandler($defaultHandler);

		$this->assertObjectHasAttribute('defaultHandler', $setDefaultHandler);

	}
}
