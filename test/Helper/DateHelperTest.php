<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
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
		$getDateObj = DateHelper::getDate('2015-12-31 23:59:59', 'Asia/Taipei');

		$this->assertInstanceOf('JDate', $getDateObj);

		$getTimeZone = $getDateObj->getTimezone()->getName();

		$this->assertEquals('Asia/Taipei', $getTimeZone);
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
		$this->markTestIncomplete(
			'Waiting for MockDate to test'
		);
	}

	/**
	 * Method of test testTimezone()
	 *
	 * @param string $utcDateTime
	 * @param string $tzOffset
	 * @param string $expected
	 *
	 * @return  void
	 *
	 * @dataProvider timeZonesProvider
	 *
	 * @covers \Windwalker\Helper\DateHelper::getDate
	 */
	public function testTimezone($utcDateTime, $tzOffset, $expected)
	{
		$inputDateTime = $expected;

		$utcDate = DateHelper::getDate($inputDateTime, $tzOffset);

		$translateToUTC = $utcDate->format('Y-m-d H:i:s');

		$this->assertEquals($utcDateTime, $translateToUTC);

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

		$this->assertRegExp('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})', $getOutput);
	}

	/**
	 * timeZonesProvider
	 *
	 * @return  array
	 */
	public function timeZonesProvider()
	{
		$utcDateTime = '2015-01-01 00:00:00';

		return array(
			array($utcDateTime ,'UTC', '2015-01-01 00:00:00'),
			array($utcDateTime ,'Asia/Taipei', '2015-01-01 08:00:00'),
			array($utcDateTime ,'Asia/Tokyo', '2015-01-01 09:00:00'),
			array($utcDateTime ,'US/Hawaii', '2014-12-31 14:00:00'),
			array($utcDateTime ,'America/Mexico_City', '2014-12-31 18:00:00'),
			array($utcDateTime ,'Australia/Sydney', '2015-01-01 11:00:00'),
		);
	}
}
