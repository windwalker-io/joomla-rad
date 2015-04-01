<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Html\HtmlBuilder;

/**
 * Test class of Windwalker\Helper\ArrayHelper
 *
 * @since 2.0
 */
class HtmlBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * testPairedTag
	 *
	 * @param $tagName
	 * @param $expect
	 * @param $id
	 * @param $className
	 *
	 * @dataProvider pairedTagProvider
	 * @covers \Windwalker\Html\HtmlBuilder::create()
	 */
	public function testPairedTag($tagName, $expect, $id, $className)
	{
		// Create element
		$attributes = array('id' => $id, 'class' => $className);
		$originElement = HtmlBuilder::create($tagName, $expect, $attributes);

		$dom = new \DOMDocument;
		$dom->loadHTML("<html><body>" . $originElement . "</body></html>");

		$domNode = $dom->getElementById($id);

		// Test attributes
		$this->assertInstanceOf('DOMNode', $domNode);
		$this->assertEquals($className, $domNode->getAttribute('class'));

		$domNode->removeAttribute('id');
		$domNode->removeAttribute('class');

		// Convert DOMNode to string
		$element = $dom->saveHTML($domNode);

		// Remove redundant white spaces
		$trimmedElement = preg_replace('/\s+/', ' ', $element);

		$pattern = '#<' . $tagName . '>(.*?)</' . $tagName . '>#';
		$matchResult = preg_match($pattern, $trimmedElement, $matches);

		// Test open and close tag and innerText
		$this->assertTrue(true, $matchResult);
		$this->assertEquals($expect, trim($matches[1]));
	}

	/**
	 * testSingleTag
	 *
	 * @param $tagName
	 * @param $id
	 * @param $className
	 *
	 * @dataProvider singleTagProvider
	 * @covers \Windwalker\Html\HtmlBuilder::create()
	 */
	public function testSingleTag($tagName, $id, $className)
	{
		$attributes = array('id' => $id, 'class' => $className);
		$originElement = HtmlBuilder::create($tagName, '', $attributes);

		$dom = new \DOMDocument;
		$dom->loadHTML("<html><body>" . $originElement . "</body></html>");

		$domNode = $dom->getElementById($id);

		// Test attributes
		$this->assertInstanceOf('DOMNode', $domNode);
		$this->assertEquals($className, $domNode->getAttribute('class'));

		$domNode->removeAttribute('id');
		$domNode->removeAttribute('class');

		// Convert DOMNode to string
		$element = $dom->saveHTML($domNode);

		$pattern = '#<' . $tagName . ' />#';
		$matchResult = preg_match($pattern, $element, $matches);

		// Text open tag
		$this->assertTrue(true, $matchResult);
	}

	/**
	 * pairedTagProvider
	 *
	 * @return  array
	 */
	public function pairedTagProvider()
	{
		$innerText = 'Hello world!';
		$id = 'test-id';
		$className = 'test-class';

		return array(
			array('p', $innerText, $id, $className),
			array('a', $innerText, $id, $className),
			array('div', $innerText, $id, $className),
			array('h1', $innerText, $id, $className),
			array('form', $innerText, $id, $className),
			array('li', $innerText, $id, $className),
			array('ul', $innerText, $id, $className),
			array('table', $innerText, $id, $className),
			array('style', $innerText, $id, $className),
			array('script', $innerText, $id, $className)
		);
	}

	/**
	 * singleTagProvider
	 *
	 * @return  array
	 */
	public function singleTagProvider()
	{
		$id = 'test-id';
		$className = 'test-class';

		return array(
			array('input', $id, $className),
			array('img', $id, $className),
			array('br', $id, $className),
			array('hr', $id, $className),
			array('area', $id, $className),
			array('param', $id, $className),
			array('base', $id, $className),
			array('link', $id, $className),
			array('meta', $id, $className),
			array('option', $id, $className)
		);
	}
}
