<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\HtmlHelper;
use Windwalker\Test\TestCase\DomTestCase;

/**
 * Test class of HtmlHelper
 *
 * @since 2.1
 */
class HtmlHelperTest extends DomTestCase
{
	/**
	 * Method to test HtmlHelper::repair for closed HTML tags with Tidy.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlClosedTidyDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 * @group        repair
	 *
	 * @requires extension tidy
	 */
	public function testRepairHtmlClosedTidy($expected, $data)
	{
		if (!function_exists('tidy_repair_string'))
		{
			$this->markTestSkipped(
				'The Tidy extension is not available.'
			);

			return;
		}

		$this->assertDomStringEqualsDomString($expected, HtmlHelper::repair($data));
	}

	/**
	 * Method to test HtmlHelper::repair for closed HTML tags.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlClosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 * @group        repair
	 */
	public function testRepairHtmlClosed($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data, false));
	}

	/**
	 * repairHtmlClosedTidyDataProvider
	 *
	 * @return array $returns
	 */
	public function repairHtmlClosedTidyDataProvider()
	{
		$returns = array();

		$returns[0] = array();
		$returns[1] = array();
		$returns[2] = array();

		$returns[0][0] = <<<EXPECTED_1
<p>
  Over my dead body
</p>
EXPECTED_1;
		$returns[0][1] = <<<DATA_1
<p>
  Over my dead body
</p>
DATA_1;

		$returns[1][0] = <<<EXPECTED_2
<div>
  <p>
    Over my dead body
  </p>
</div>
EXPECTED_2;
		$returns[1][1] = <<<DATA_2
<div>
  <p>
    Over my dead body
  </p>
</div>
DATA_2;

		$returns[2][0] = <<<EXPECTED_3
<table>
  <tr>
    <td>
      Over my dead body
    </td>
  </tr>
</table>
EXPECTED_3;
		$returns[2][1] = <<<DATA_3
      <table>
  <tr><td>
    Over my dead body</td>
    </tr>
 </table>
DATA_3;

		return $returns;
	}

	/**
	 * repairHtmlClosedDataProvider
	 *
	 * @return array $returns
	 */
	public function repairHtmlClosedDataProvider()
	{
		$returns = array();

		$returns[0] = array();
		$returns[1] = array();
		$returns[2] = array();

		$returns[0][0] = <<<EXPECTED_1
<p>
  Over my dead body
</p>
EXPECTED_1;
		$returns[0][1] = <<<DATA_1
<p>
  Over my dead body
</p>
DATA_1;

		$returns[1][0] = <<<EXPECTED_2
<div>
  <p>
    Over my dead body
  </p>
</div>
EXPECTED_2;
		$returns[1][1] = <<<DATA_2
<div>
  <p>
    Over my dead body
  </p>
</div>
DATA_2;

		$returns[2][0] = <<<EXPECTED_3
      <table>
  <tr><td>
    Over my dead body</td>
    </tr>
 </table>
EXPECTED_3;
		$returns[2][1] = <<<DATA_3
      <table>
  <tr><td>
    Over my dead body</td>
    </tr>
 </table>
DATA_3;

		return $returns;
	}

	/**
	 * Method to test HtmlHelper::repair for unclosed HTML tags with Tidy.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnclosedTidyDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 * @group        repair
	 *
	 * @requires extension tidy
	 */
	public function testRepairHtmlUnclosedTidy($expected, $data)
	{
		if (!function_exists('tidy_repair_string'))
		{
			$this->markTestSkipped(
				'The Tidy extension is not available.'
			);

			return;
		}

		$this->assertDomStringEqualsDomString($expected, HtmlHelper::repair($data));
	}

	/**
	 * Method to test HtmlHelper::repair for unclosed HTML tags.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnclosedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 * @group        repair
	 */
	public function testRepairHtmlUnclosed($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data, false));
	}

	/**
	 * repairHtmlUnclosedTidyDataProvider
	 *
	 * @return array $returns
	 */
	public function repairHtmlUnclosedTidyDataProvider()
	{
		$returns = array();

		$returns[0] = array();
		$returns[1] = array();

		$returns[0][0] = <<<EXPECTED_1
<p>
  Over my dead body
</p>
EXPECTED_1;
		$returns[0][1] = <<<DATA_1
<p>
  Over my dead body
DATA_1;

		$returns[1][0] = <<<EXPECTED_2
<div>
  <p>
    Over my dead body
  </p>
</div>
EXPECTED_2;
		$returns[1][1] = <<<DATA_2
<div>
  <p>
    Over my dead body
</div>
DATA_2;

		return $returns;
	}

	/**
	 * repairHtmlUnclosedDataProvider
	 *
	 * @return array $returns
	 */
	public function repairHtmlUnclosedDataProvider()
	{
		$returns = array();

		$returns[0] = array();
		$returns[1] = array();

		$returns[0][0] = <<<EXPECTED_1
<p>
  Over my dead body</p>
EXPECTED_1;
		$returns[0][1] = <<<DATA_1
<p>
  Over my dead body
DATA_1;

		$returns[1][0] = <<<EXPECTED_2
<div>
  <p>
    Over my dead body
</div></p>
EXPECTED_2;
		$returns[1][1] = <<<DATA_2
<div>
  <p>
    Over my dead body
</div>
DATA_2;

		return $returns;
	}

	/**
	 * Method to test HtmlHelper::repair for unopened HTML tags with Tidy.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnopenedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 * @group        repair
	 *
	 * @requires extension tidy
	 */
	public function testRepairHtmlUnopenedTidy($expected, $data)
	{
		if (!function_exists('tidy_repair_string'))
		{
			$this->markTestSkipped(
				'The Tidy extension is not available.'
			);

			return;
		}

		$this->assertNotSame($expected, HtmlHelper::repair($data));
	}

	/**
	 * Method to test HtmlHelper::repair for unopened HTML tags.
	 *
	 * @param string $expected
	 * @param string $data
	 *
	 * @return void
	 *
	 * @dataProvider repairHtmlUnopenedDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::repair
	 * @group        repair
	 */
	public function testRepairHtmlUnopened($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::repair($data, false));
	}

	/**
	 * repairHtmlUnopenedDataProvider
	 *
	 * @return array $returns
	 */
	public function repairHtmlUnopenedDataProvider()
	{
		$returns = array();

		$returns[0] = array();
		$returns[1] = array();

		$returns[0][0] = <<<EXPECTED_1
  Over my dead body
</p>
EXPECTED_1;
		$returns[0][1] = <<<DATA_1
  Over my dead body
</p>
DATA_1;

		$returns[1][0] = <<<EXPECTED_2
<div>
    Over my dead body
  </p>
</div>
EXPECTED_2;
		$returns[1][1] = <<<DATA_2
<div>
    Over my dead body
  </p>
</div>
DATA_2;

		return $returns;
	}

	/**
	 * Method to test HtmlHelper::getJSObject.
	 *
	 * @param string $expected
	 * @param array $data
	 *
	 * @return void
	 *
	 * @dataProvider getJSObjectDataProvider
	 * @covers       Windwalker\Helper\HtmlHelper::getJSObject
	 * @group        getJSObject
	 */
	public function testGetJSObject($expected, $data)
	{
		$this->assertSame($expected, HtmlHelper::getJSObject($data));
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
				'{"foo": "bar"}',
				array('foo' => 'bar'),
			),
			array(
				'{"goo": 23,"hoo": true,"joo": {"koo": "car"}}',
				array(
					'goo' => 23,
					'hoo' => true,
					'ioo' => null,
					'joo' => array('koo' => 'car'),
				),
			),
		);
	}
}
