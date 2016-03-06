<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Data\Data;
use Windwalker\Helper\ReflectionHelper;

/**
 * TestClass - A Mock Class
 */
class TestClass
{
	/**
	 * A test static method
	 *
	 * @return  string
	 */
	public function foo()
	{
		return 'bar';
	}
}

/**
 * Test class of Windwalker\Helper\ReflectionHelper
 *
 * @since 2.0
 */
class ReflectionHelperTest extends \PHPUnit_Framework_TestCase
{
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
	 * Method to test get().
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Helper\ReflectionHelper::get
	 */
	public function testGet()
	{
		$this->assertInstanceOf('ReflectionClass', ReflectionHelper::get('PDO'));
		$this->assertInstanceOf('ReflectionClass', ReflectionHelper::get('ReflectionClass'));
		$this->assertInstanceOf('ReflectionClass', ReflectionHelper::get('Windwalker\Test\Helper\TestClass'));
		$this->assertInstanceOf('ReflectionClass', ReflectionHelper::get(new Data));
	}

//	/**
//	 * Method to test exceptions in get() when input is an Array
//	 *
//	 * @return  void
//	 *
//	 * @expectedException \InvalidArgumentException
//	 * @covers \Windwalker\Helper\ReflectionHelper::get
//	 */
//	public function testGetExceptionWithArray()
//	{
//		ReflectionHelper::get(array());
//	}

	/**
	 * Method to test exceptions in get() when input is a float number
	 *
	 * @return  void
	 *
	 * @expectedException \ReflectionException
	 * @covers \Windwalker\Helper\ReflectionHelper::get
	 */
	public function testGetExceptionWithFloat()
	{
		ReflectionHelper::get(12345.678);
	}

	/**
	 * Method to test exceptions in get() when input is an invalid class name
	 *
	 * @return  void
	 *
	 * @expectedException \ReflectionException
	 * @covers \Windwalker\Helper\ReflectionHelper::get
	 */
	public function testGetExceptionWhenClassNotExists()
	{
		ReflectionHelper::get('ClassNotExists');
	}

	/**
	 * Method to test getPath().
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Helper\ReflectionHelper::getPath
	 */
	public function testGetPath()
	{
		$this->assertEquals(__FILE__, ReflectionHelper::getPath($this));
		$this->assertEquals(__FILE__, ReflectionHelper::getPath(new TestClass));
	}

	/**
	 * Method to test magic method __callStatic.
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Helper\ReflectionHelper::__callStatic
	 */
	public function testMagicMethodCallStatic()
	{
		$testClassNamespace = 'Windwalker\Test\Helper';
		$testClassName = $testClassNamespace . '\TestClass';

		$this->assertEquals($testClassName, ReflectionHelper::getName($testClassName));
		$this->assertEquals($testClassNamespace, ReflectionHelper::getNamespaceName($testClassName));
	}
}
