<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\DateHelper;

/**
 * Class DateHelperTest
 *
 * @since 1.0
 */
class DateHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method of test testJDate().
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Helper\DateHelper::getDate
	 */
	public function testJDate()
	{
		$JDate = new \JDate();

		$getDateObj = DateHelper::getDate();

		$this->assertEquals($getDateObj, $JDate);
	}

	/**
	 * Method of test testFormatForNow()
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Helper\DateHelper::getDate
	 */
	public function testFormatForNow()
	{
		$format = 'Y-m-d';

		$getDateNowObject = DateHelper::getDate('now');

		$getDateNowFormat = $getDateNowObject->format($format);

		$this->assertInternalType('string', $getDateNowFormat);

		$phpDate = date($format);

		$this->assertInternalType('string', $phpDate);

		$this->assertEquals($getDateNowFormat, $phpDate);

		$this->markTestIncomplete(
			'Waiting for MockDate to test, and refactor'
		);
	}

	/**
	 * Method of test testTimezone()
	 *
	 * @return  void
	 *
	 * @dataProvider timeZonesProvider
	 *
	 * @covers \Windwalker\Helper\DateHelper::getDate
	 */
	public function testTimezone($tzOffset, $expected)
	{
		$inputDateTime = $expected;

		$utcDate = DateHelper::getDate($inputDateTime, $tzOffset);

		$translateToUTC = $utcDate->format('Y-m-d H:i:s');

		$this->assertEquals('2015-01-01 00:00:00', $translateToUTC);

		$localDate = DateHelper::getDate($translateToUTC, 'UTC');

		$localDate->setTimezone(new \DateTimeZone($tzOffset));

		$localDateTime = $localDate->format('Y-m-d H:i:s', true);

		$this->assertEquals($localDateTime, $expected);
	}

	/**
	 * Method of test testNullInput()
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\DateHelper::getDate
	 */
	public function testNullInput()
	{
		$getOutput = DateHelper::getDate(null, null)->format('Y-m-d H:i:s');

		$this->assertInternalType('string', $getOutput);
	}

	/**
	 * timeZonesProvider
	 *
	 * @return  array
	 */
	public function timeZonesProvider()
	{
		return array(
			array('UTC', '2015-01-01 00:00:00'),
			array('Asia/Taipei', '2015-01-01 08:00:00'),
			array('Asia/Tokyo', '2015-01-01 09:00:00'),
			array('US/Hawaii', '2014-12-31 14:00:00'),
			array('America/Mexico_City', '2014-12-31 18:00:00'),
			array('Australia/Sydney', '2015-01-01 11:00:00'),
		);
	}
}
