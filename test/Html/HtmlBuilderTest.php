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
	 * testCreate
	 *
	 * @covers \Windwalker\Html\HtmlBuilder::create()
	 */
	public function testCreate()
	{
		// Prepare attributes, (目前加 $attributes 參數 render 後會多一個雙引號, 之後再補上相關 test)
		$id = 'test-id';
		$className = 'test-class';
		$attributes = ['id' => $id, 'class' => $className];

		// Test paired tags
		$pairedTags = $this->getPairedTags();

		foreach ($pairedTags as $pairedTag)
		{
			$tagName = $pairedTag;
			$innerText = 'hello world';

			$element = HtmlBuilder::create($tagName, $innerText);

			// Remove redundant white spaces
			$trimmedElement = preg_replace('/\s+/', ' ', $element);

			$pattern = '#<' . $tagName . '>(.*?)</' . $tagName . '>#';
			$matchResult = preg_match($pattern, $trimmedElement, $matches);

			// Test open and close tag and innerText
			$this->assertTrue(true, $matchResult);
			$this->assertEquals($innerText, trim($matches[1]));
		}

		// Check single tag
		$singleTags = $this->getSingleTags();

		foreach ($singleTags as $singleTag)
		{
			$tagName = $singleTag;

			$element = HtmlBuilder::create($tagName);

			$pattern = '#<' . $tagName . ' />#';
			$matchResult = preg_match($pattern, $element, $matches);

			// Check open tag
			$this->assertTrue(true, $matchResult);
		}
	}

	/**
	 * getPairedTags
	 *
	 * @return  array
	 */
	public function getPairedTags()
	{
		return array('p', 'a', 'div', 'h1', 'form', 'li', 'ul', 'table', 'style', 'script');
	}

	/**
	 * getSingleTags
	 *
	 * @return  array
	 */
	public function getSingleTags()
	{
		return array('input', 'img');
	}
}
