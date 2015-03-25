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
	 * @param string $html_string_closed
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlClosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlClosedTidy($html_string_closed)
	{
		$this->assertSame($html_string_closed, HtmlHelper::repair($html_string_closed));
	}

	/**
	 * Method to test repair() for closed HTML tags.
	 *
	 * @param string $html_string_closed
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlClosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlClosed($html_string_closed)
	{
		$this->assertSame($html_string_closed, HtmlHelper::repair($html_string_closed, false));
	}

	/**
	 * repairHtmlClosedDataProvider
	 *
	 * @return array
	 */
	public function repairHtmlClosedDataProvider()
	{
		return array(
			array(<<<HTML_STR_CLOSED_1
<p>
  Over my dead body
</p>
HTML_STR_CLOSED_1
),
			array(<<<HTML_STR_CLOSED_2
<div>
  <p>
    Over my dead body
  </p>
</div>
HTML_STR_CLOSED_2
),
		);
	}

	/**
	 * Method to test repair() for unclosed HTML tags with Tidy.
	 *
	 * @param string $html_string_unclosed
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnclosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnclosedTidy($html_string_unclosed)
	{
		$this->assertNotSame($html_string_unclosed, HtmlHelper::repair($html_string_unclosed));
	}

	/**
	 * Method to test repair() for unclosed HTML tags.
	 *
	 * @param string $html_string_unclosed
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnclosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnclosed($html_string_unclosed)
	{
		$this->assertNotSame($html_string_unclosed, HtmlHelper::repair($html_string_unclosed, false));
	}

	/**
	 * repairHtmlUnclosedDataProvider
	 *
	 * @return array
	 */
	public function repairHtmlUnclosedDataProvider()
	{
		return array(
			array(<<<HTML_STR_UNCLOSED_1
<p>
  Over my dead body
HTML_STR_UNCLOSED_1
),
			array(<<<HTML_STR_UNCLOSED_2
<div>
  <p>
    Over my dead body
</div>
HTML_STR_UNCLOSED_2
),
		);
	}

	/**
	 * Method to test repair() for unopened HTML tags with Tidy.
	 *
	 * @param string $html_string_unopened
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnopenedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnopenedTidy($html_string_unopened)
	{
		$this->assertNotSame($html_string_unopened, HtmlHelper::repair($html_string_unopened));
	}

	/**
	 * Method to test repair() for unopened HTML tags.
	 *
	 * @param string $html_string_unopened
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnopenedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 */
	public function testRepairHtmlUnopened($html_string_unopened)
	{
		$this->assertSame($html_string_unopened, HtmlHelper::repair($html_string_unopened, false));
	}

	/**
	 * repairHtmlUnopenedDataProvider
	 *
	 * @return array
	 */
	public function repairHtmlUnopenedDataProvider()
	{
		return array(
			array(<<<HTML_STR_UNOPENED_1
  Over my dead body
</p>
HTML_STR_UNOPENED_1
),
			array(<<<HTML_STR_UNOPENED_2
<div>
    Over my dead body
  </p>
</div>
HTML_STR_UNOPENED_2
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
