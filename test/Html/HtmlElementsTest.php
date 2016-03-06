<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Html;

use Windwalker\Html\HtmlElement;
use Windwalker\Html\HtmlElements;
use Windwalker\Test\Helper\DomHelper;

/**
 * Test class of Windwalker\Html\HtmlElements
 *
 * @since 2.0
 */
class HtmlElementsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * test__toString
	 *
	 * @param $top
	 * @param $mid
	 * @param $bottom
	 * @param $expect
	 *
	 * @covers \Windwalker\Html\HtmlElements
	 * @dataProvider htmlProvider
	 */
	public function test__toString($top, $mid, $bottom, $expect)
	{
		$this->assertEquals(DomHelper::minify($expect), DomHelper::minify(new HtmlElements(array($top, $mid, $bottom))));
	}

	/**
	 * htmlProvider
	 *
	 * @return  array
	 */
	public function htmlProvider()
	{
		return array(
			array(
				new HtmlElement('option', 'foo'),
				new HtmlElement('option', 'bar'),
				new HtmlElement('rdf:metaData', new HtmlElement('rdf:name', 'Tim')),
				'<option>foo</option><option>bar</option><rdf:metaData><rdf:name>Tim</rdf:name></rdf:metaData>'
			),
			array(
				new HtmlElement('img', null, array('src' => 'http://placehold.it/100x100')),
				new HtmlElement('input'),
				new HtmlElement('div', 'Hello world'),
				'<img src="http://placehold.it/100x100" /><input /><div>Hello world</div>'
			)
		);
	}
}
