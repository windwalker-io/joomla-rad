<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Html;

use Windwalker\Html\HtmlElement;
use Windwalker\Html\HtmlBuilder;
use Windwalker\Test\Helper\DomHelper;

/**
 * Test class of Windwalker\Helper\ArrayHelper
 *
 * @since 2.0
 */
class HtmlElementTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * testToString
	 *
	 * @param $tagName
	 * @param $content
	 * @param $attributes
	 * @param $expect
	 *
	 * @covers \Windwalker\Html\HtmlElement::__toString()
	 * @dataProvider htmlProvider
	 */
	public function test__toString($tagName, $content, $attributes, $expect)
	{
		$html = new HtmlElement($tagName, $content, $attributes);

		$this->assertEquals(
			DomHelper::minify($expect),
			DomHelper::minify($html)
		);
	}

	/**
	 * testGetContent
	 *
	 * @param $tagName
	 * @param $content
	 * @param $expect
	 *
	 * @covers \Windwalker\Html\HtmlElement::getContent()
	 * @dataProvider contentProvider
	 */
	public function testGetContent($tagName, $content, $expect)
	{
		$html = new HtmlElement($tagName, $content);

		$this->assertEquals($expect, DomHelper::minify($html->getContent()));
	}

	/**
	 * testSetContent
	 *
	 * @param $tagName
	 * @param $content
	 *
	 * @covers \Windwalker\Html\HtmlElement::setContent()
	 * @dataProvider contentProvider
	 */
	public function testSetContent($tagName, $content)
	{
		$html = new HtmlElement($tagName, $content);
		$html->setContent('World hello');

		$this->assertEquals('World hello', DomHelper::minify($html->getContent()));
	}

	/**
	 * testGetAttribute
	 *
	 * @covers \Windwalker\Html\HtmlElement::getAttribute()
	 */
	public function testGetAttribute()
	{
		$html = new HtmlElement('div', '', array('id' => 'test-id', 'class' => 'test-class'));

		$this->assertEquals('test-id', $html->getAttribute('id'));
		$this->assertEquals('test-class', $html->getAttribute('class'));
	}

	/**
	 * testSetAttribute
	 *
	 * @covers \Windwalker\Html\HtmlElement::setAttribute()
	 */
	public function testSetAttribute()
	{
		$html = new HtmlElement('div', '', array('id' => 'test-id', 'class' => 'test-class'));
		$html->setAttribute('id', 'foo');
		$html->setAttribute('class', 'bar');

		$this->assertEquals('foo', $html->getAttribute('id'));
		$this->assertEquals('bar', $html->getAttribute('class'));
		$this->assertEquals('<div id="foo" class="bar"></div>', DomHelper::minify($html));
	}

	/**
	 * testGetAttributes
	 *
	 * @covers \Windwalker\Html\HtmlElement::getAttributes()
	 */
	public function testGetAttributes()
	{
		$html = new HtmlElement('div', '', array('id' => 'test-id', 'class' => 'test-class'));

		$this->assertEquals(array('id' => 'test-id', 'class' => 'test-class'), $html->getAttributes());
	}

	/**
	 * testSetAttributes
	 *
	 * @covers \Windwalker\Html\HtmlElement::setAttributes()
	 */
	public function testSetAttributes()
	{
		$newAttributes = array('id' => 'foo', 'class' => 'bar');

		// Test tag with attributes
		$html = new HtmlElement('div', '', array('id' => 'test-id', 'class' => 'test-class'));
		$html->setAttributes($newAttributes);

		$this->assertEquals($newAttributes, $html->getAttributes());
		$this->assertEquals('<div id="foo" class="bar"></div>', DomHelper::minify($html));
	}

	/**
	 * testGetName
	 *
	 * @covers \Windwalker\Html\HtmlElement::getName()
	 */
	public function testGetName()
	{
		$html = new HtmlElement('div');

		$this->assertEquals('div', $html->getName());
	}

	/**
	 * testSetName
	 *
	 * @covers \Windwalker\Html\HtmlElement::setName()
	 */
	public function testSetName()
	{
		$html = new HtmlElement('div', null, array('id' => 'test-id', 'class' => 'test-class'));
		$html->setName('input');

		$this->assertEquals('<input id="test-id" class="test-class" />', DomHelper::minify($html));
	}

	/**
	 * testOffsetExist
	 *
	 * @covers \Windwalker\Html\HtmlElement::offsetExists()
	 */
	public function testOffsetExist()
	{
		$html = new HtmlElement('div', '', array('id' => 'test-id'));

		$this->assertTrue($html->offsetExists('id'));
		$this->assertNotTrue($html->offsetExists('class'));
	}

	/**
	 * testOffsetGet
	 *
	 * @covers \Windwalker\Html\HtmlElement::offsetGet()
	 */
	public function testOffsetGet()
	{
		$html = new HtmlElement('div', '', array('id' => 'test-id'));

		$this->assertEquals('test-id', $html->offsetGet('id'));
		$this->assertNull($html->offsetGet('class'));
	}

	/**
	 * testOffsetSet
	 *
	 * @covers \Windwalker\Html\HtmlElement::offsetSet()
	 */
	public function testOffsetSet()
	{
		$html = new HtmlElement('div');

		$html->offsetSet('id', 'test-id');

		$this->assertEquals('test-id', $html->offsetGet('id'));
		$this->assertEquals('<div id="test-id"></div>', DomHelper::minify($html));
	}

	/**
	 * testOffsetUnset
	 *
	 * @covers \Windwalker\Html\HtmlElement::offsetUnset()
	 */
	public function testOffsetUnset()
	{
		$html = new HtmlElement('div', '', array('id' => 'test-id', 'class' => 'test-class'));

		$html->offsetUnset('id');

		$this->assertNull($html->offsetGet('id'));
		$this->assertEquals('<div class="test-class"></div>', DomHelper::minify($html));
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

	/**
	 * contentProvider
	 *
	 * @return  array
	 */
	public function contentProvider()
	{
		$string = 'Hello world';

		return array(
			array('div', $string, $string),
			array('ul', HtmlBuilder::create('li', $string), '<li>' . $string . '</li>')
		);
	}
}
