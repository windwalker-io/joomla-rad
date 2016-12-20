<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Uuid;

use Windwalker\Uuid\Uuid;

/**
 * Test class of Windwalker\Uuid\UuidTest
 *
 * @since 2.0
 */
class UuidTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test v3().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Uuid\Uuid::v3
	 */
	public function testV3()
	{
		$this->assertEquals('35558b18-b93c-36a1-885d-69d7e34e31e8', Uuid::v3(Uuid::NAMESPACE_DNS, 'foo.bar.com'));

		$this->assertEquals('29a604ca-bbcb-3c7a-934c-8817f250545d', Uuid::v3(Uuid::NAMESPACE_URL, 'http://foo.bar.com/foobar/'));

		$this->assertEquals('2b115a8e-b8a4-3b8b-983d-ce50c46ca24b', Uuid::v3(Uuid::NAMESPACE_X500, 'cn=SMS Taiwan,ou=Company,o=Healthy,c=TW'));
	}

	/**
	 * Method to test v4().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Uuid\Uuid::v4
	 */
	public function testV4()
	{
		$expect = '76f0030c-88bd-4fdc-a09c-47d8483b2296';

		self::assertTrue(Uuid::isValid($expect));
	}

	/**
	 * Method to test v5().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Uuid\Uuid::v5
	 */
	public function testV5()
	{
		$this->assertEquals('42966b0e-da16-5a1c-8fbf-487dcac94fe8', Uuid::v5(Uuid::NAMESPACE_DNS, 'foo.bar.com'));

		$this->assertEquals('c4fc7437-a5c8-53d3-ac95-36bbc71fc931', Uuid::v5(Uuid::NAMESPACE_URL, 'http://foo.bar.com/foobar/'));

		$this->assertEquals('5d9c8d79-c265-5398-b135-2a97382eb7df', Uuid::v5(Uuid::NAMESPACE_X500, 'cn=SMS Taiwan,ou=Company,o=Healthy,c=TW'));
	}
}
