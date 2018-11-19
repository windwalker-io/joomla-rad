<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Object;

use Windwalker\Object\BaseObject;

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
	 * @param \Windwalker\Object\BaseObject $object
	 * @param bool                          $expect
	 *
	 * @return void
	 *
	 * @covers       \Windwalker\Object\BaseObject::isNull
	 * @dataProvider objectProvider
	 */
	public function testIsNull(BaseObject $object, $expect)
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
			new BaseObject($properties),
			(boolean) $properties,
		);

		$properties = array(
			'foo' => 'bar',
			'bar' => 'foo',
		);
		$data[] = array(
			new BaseObject($properties),
			(boolean) $properties,
		);

		$properties = (object) array(
			'foo' => 'bar',
			'bar' => 'foo',
		);
		$data[] = array(
			new BaseObject($properties),
			(boolean) $properties,
		);

		return $data;
	}
}
