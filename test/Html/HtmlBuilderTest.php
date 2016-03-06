<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Html;

use Windwalker\Html\HtmlBuilder;

/**
 * Test class of Windwalker\Helper\ArrayHelper
 *
 * @since 2.0
 */
class HtmlBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * testCreate
	 *
	 * @param string $tagName
	 * @param string $content
	 * @param string $attributes
	 * @param string $expect
	 *
	 * @covers \Windwalker\Html\HtmlBuilder::create()
	 * @dataProvider htmlProvider
	 *
	 * @return void
	 */
	public function testCreate($tagName, $content, $attributes, $expect)
	{
		$html = HtmlBuilder::create($tagName, $content, $attributes);

		// See http://stackoverflow.com/questions/6225351/how-to-minify-php-page-html-output
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);

		$replace = array(
			'>',
			'<',
			'\\1'
		);

		$this->assertEquals(
			preg_replace($search, $replace, $expect),
			preg_replace($search, $replace, $html)
		);
	}

	/**
	 * htmlProvider
	 *
	 * @return  array
	 */
	public function htmlProvider()
	{
		return array(
			// paired tag
			array(
				'p',
				'Hello world',
				array('id' => 'test-id', 'class' => 'test-class'),
				'<p id="test-id" class="test-class">Hello world</p>'
			),
			// ul>li*2
			array(
				'ul',
				HtmlBuilder::create('li', 'Hello world', array()) . HtmlBuilder::create('li', 'Hello world', array()),
				array('id' => 'test-id', 'class' => 'test-class'),
				'<ul id="test-id" class="test-class">
					<li>Hello world</li>
					<li>Hello world</li>
				</ul>'
			),
			// select>option*2
			array(
				'select',
				HtmlBuilder::create('option', 'BOY', array('value' => 1, 'selected' => true))
				. HtmlBuilder::create('option', 'GIRL', array('value' => 2)),
				array('id' => 'test-id', 'class' => 'test-class'),
				'<select id="test-id" class="test-class">
					<option value="1" selected="selected">BOY</option>
					<option value="2">GIRL</option>
				</select>'
			),
			// single tag without content
			array(
				'img',
				null,
				array('id' => 'test-id', 'class' => 'test-class', 'src' => 'http://placehold.it/100x100'),
				'<img id="test-id" class="test-class" src="http://placehold.it/100x100" />'
			),
			// br tag
			array(
				'br',
				null,
				array(),
				'<br />'
			),
			// HTML5
			array(
				'video',
				'',
				array('id' => 'test-id', 'controls' => true, 'muted' => true),
				'<video id="test-id" controls muted></video>'
			)
		);
	}
}
