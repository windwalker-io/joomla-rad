<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Object;

use Windwalker\Object\NullObject;

/**
 * Test class of Windwalker\Object\NullObject
 *
 * @since 2.0
 */
class NullObjectTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test isNull().
	 *
	 * @param NullObject $object
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Object\NullObject::isNull
	 * @dataProvider objectProvider
	 */
	public function testIsNull(NullObject $object)
	{
		$this->assertTrue($object->isNull());
	}

	/**
	 * Method to test __toString().
	 *
	 * @param NullObject $object
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Object\NullObject::__toString
	 * @dataProvider objectProvider
	 */
	public function test__toString(NullObject $object)
	{
		$this->assertSame('', (string) $object);
	}

	/**
	 * Method to test get().
	 *
	 * @param NullObject $object
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Object\NullObject::get
	 * @dataProvider objectProvider
	 */
	public function testGet(NullObject $object)
	{
		$this->assertSame('default', $object->get('foo', 'default'));
	}

	/**
	 * Method to test getProperties().
	 *
	 * @param NullObject $object
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Object\NullObject::getProperties
	 * @dataProvider objectProvider
	 */
	public function testGetProperties(NullObject $object)
	{
		$this->assertSame(array(), $object->getProperties());
		$this->assertSame(array(), $object->getProperties(true));
	}

	/**
	 * Method to test setProperties().
	 *
	 * @param NullObject $object
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Object\NullObject::setProperties
	 * @dataProvider objectProvider
	 */
	public function testSetProperties(NullObject $object)
	{
		$this->assertSame(false, $object->setProperties(array('bar' => 'foo')));
	}

	/**
	 * Method to test __call().
	 *
	 * @param NullObject $object
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Object\NullObject::__call
	 * @dataProvider objectProvider
	 */
	public function test__call(NullObject $object)
	{
		$this->assertNull($object->unknownMethod());
	}

	/**
	 * Method to test __get().
	 *
	 * @param NullObject $object
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Object\NullObject::__get
	 * @dataProvider objectProvider
	 */
	public function test__get(NullObject $object)
	{
		$this->assertNull($object->unknownProperty());
	}

	/**
	 * objectProvider
	 *
	 * @return  array
	 */
	public function objectProvider()
	{
		return array(
			array(new NullObject),
			array(new NullObject(array('foo' => 'bar'))),
		);
	}
}
