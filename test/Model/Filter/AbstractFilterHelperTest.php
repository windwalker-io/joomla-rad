<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Filter;

use Windwalker\Test\Model\Stub\StubAbstractFilterHelper;

/**
 * Test class of \Windwalker\Model\Filter\AbstractFilterHelper
 *
 * @since 2.1
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
		$this->instance = new StubAbstractFilterHelper;
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
	 * @covers \Windwalker\Model\Filter\AbstractFilterHelper::__construct
	 */
	public function test__construct()
	{
		/** @var \Closure $theClosure */
		$theClosure = $this->readAttribute($this->instance, 'defaultHandler');

		$arg1 = 5;
		$arg2 = 6;

		$expected = 30;

		$result = $theClosure($arg1, $arg2);

		$this->assertSame($expected, $result);
	}

	/**
	 * Method to test setHandler().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Filter\AbstractFilterHelper::setHandler
	 */
	public function testSetHandler()
	{
		$handler = function ($arg){
			return $arg;
		};

		$name = 'winterHandler';

		$theHandler = $this->instance->setHandler($name, $handler);

		$getHandler = $this->getObjectAttribute($theHandler, 'handler');

		$this->assertArrayHasKey($name, $getHandler);

		$theClosure = $this->readAttribute($theHandler, 'handler');

		$expected = 'cold';

		$result = $theClosure[$name]($expected);

		$this->assertSame($expected, $result);
	}

	/**
	 * Method to test setDefaultHandler().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Filter\AbstractFilterHelper::setDefaultHandler
	 */
	public function testSetDefaultHandler()
	{
		$handler = function ($arg1, $arg2){
			return $arg1 + $arg2;
		};

		$theDefaultHandler = $this->instance->setDefaultHandler($handler);

		$this->assertObjectHasAttribute('defaultHandler', $theDefaultHandler);

		/** @var \Closure $theClosure */
		$theClosure = $this->readAttribute($theDefaultHandler, 'defaultHandler');

		$arg1 = 25;
		$arg2 = 14;

		$expected = 39;

		$result = $theClosure($arg1, $arg2);

		$this->assertSame($expected, $result);
	}
}
