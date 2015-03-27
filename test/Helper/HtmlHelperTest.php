<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\HtmlHelper;

/**
 * Test class of {className}
 *
 * @since {DEPLOY_VERSION}
 */
class HtmlHelperTest extends \PHPUnit_Framework_TestCase
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
	 * Method to test repair() for closed HTML tags with Tidy.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlClosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlClosedTidy($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data));
	}

	/**
	 * Method to test repair() for closed HTML tags.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlClosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlClosed($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data, false));
	}

	/**
	 * repairHtmlClosedDataProvider
	 *
	 * @return array
	 */
	public function repairHtmlClosedDataProvider()
	{
		return array(
			array(
				<<<EXPECTED_1
<p>
  Over my dead body
</p>
EXPECTED_1
,
				<<<DATA_1
<p>
  Over my dead body
</p>
DATA_1
,
			),
			array(
				<<<EXPECTED_2
<div>
  <p>
    Over my dead body
  </p>
</div>
EXPECTED_2
,
				<<<DATA_2
<div>
  <p>
    Over my dead body
  </p>
</div>
DATA_2
,
			),
		);
	}

	/**
	 * Method to test repair() for unclosed HTML tags with Tidy.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnclosedTidyDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnclosedTidy($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data));
	}

	/**
	 * Method to test repair() for unclosed HTML tags.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnclosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnclosed($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data, false));
	}

	/**
	 * repairHtmlUnclosedTidyDataProvider
	 *
	 * @return array
	 */
	public function repairHtmlUnclosedTidyDataProvider()
	{
		return array(
			array(
				<<<EXPECTED_1
<p>
  Over my dead body
</p>
EXPECTED_1
,
				<<<DATA_1
<p>
  Over my dead body
DATA_1
,
			),
			array(
				<<<EXPECTED_2
<div>
  <p>
    Over my dead body
  </p>
</div>
EXPECTED_2
,
				<<<DATA_2
<div>
  <p>
    Over my dead body
</div>
DATA_2
,
			),
		);
	}

	/**
	 * repairHtmlUnclosedDataProvider
	 *
	 * @return array
	 */
	public function repairHtmlUnclosedDataProvider()
	{
		return array(
			array(
				<<<EXPECTED_1
<p>
  Over my dead body</p>
EXPECTED_1
,
				<<<DATA_1
<p>
  Over my dead body
DATA_1
,
			),
			array(
				<<<EXPECTED_2
<div>
  <p>
    Over my dead body
</div></p>
EXPECTED_2
,
				<<<DATA_2
<div>
  <p>
    Over my dead body
</div>
DATA_2
,
			),
		);
	}

	/**
	 * Method to test repair() for unopened HTML tags with Tidy.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnopenedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnopenedTidy($expected, $data)
	{
		$this->assertNotSame($expected, HtmlHelper::repair($data));
	}

	/**
	 * Method to test repair() for unopened HTML tags.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnopenedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnopened($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data, false));
	}

	/**
	 * repairHtmlUnopenedDataProvider
	 *
	 * @return array
	 */
	public function repairHtmlUnopenedDataProvider()
	{
		return array(
			array(
				<<<EXPECTED_1
  Over my dead body
</p>
EXPECTED_1
,
				<<<DATA_1
  Over my dead body
</p>
DATA_1
,
			),
			array(
				<<<EXPECTED_2
<div>
    Over my dead body
  </p>
</div>
EXPECTED_2
,
				<<<DATA_2
<div>
    Over my dead body
  </p>
</div>
DATA_2
,
			),
		);
	}

	/**
	 * Method to test getJSObject().
	 *
	 * @param array $array
	 *
	 * @return void
	 *
	 * @dataProvider getJSObjectDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::getJSObject
	 */
	public function testGetJSObject($array)
	{
		json_decode(HtmlHelper::getJSObject($array));
		$this->assertTrue(json_last_error() === JSON_ERROR_NONE);
	}

	/**
	 * repairHtmlUnopenedDataProvider
	 *
	 * @return array
	 */
	public function getJSObjectDataProvider()
	{
		return array(
			array(
				array('foo' => 'bar')
			),
			array(
				array(
					'goo' => 23,
					'hoo' => true,
					'ioo' => null,
					'joo' => array('koo' => 'car'),
				)
			),
		);
	}
}
