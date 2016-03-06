<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Object;

use Windwalker\Object\Object;

/**
 * Test class of Windwalker\Object\Object
 *
 * @since 2.0
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test isNull().
	 *
	 * @param \Windwalker\Object\Object $object
	 * @param bool                      $expect
	 *
	 * @return void
	 *
	 * @covers       \Windwalker\Object\Object::isNull
	 * @dataProvider objectProvider
	 */
	public function testIsNull(Object $object, $expect)
	{
		$this->assertSame($expect, $object->isNull());
	}

	/**
	 * objectProvider
	 *
	 * @return  array
	 */
	public function objectProvider()
	{
		$data = array();

		$properties = null;
		$data[] = array(
			new Object($properties),
			(boolean) $properties,
		);

		$properties = array(
			'foo' => 'bar',
			'bar' => 'foo',
		);
		$data[] = array(
			new Object($properties),
			(boolean) $properties,
		);

		$properties = (object) array(
			'foo' => 'bar',
			'bar' => 'foo',
		);
		$data[] = array(
			new Object($properties),
			(boolean) $properties,
		);

		return $data;
	}
}
