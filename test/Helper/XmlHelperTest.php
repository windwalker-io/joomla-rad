<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\XmlHelper;

/**
 * Class XmlHelperTest
 *
 * @since 1.0
 */
class XmlHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method test of get()
	 *
	 * @param \SimpleXMLElement $element
	 * @param string            $attr
	 * @param string            $value
	 * @param string            $expected
	 *
	 * @return  void
	 *
	 * @dataProvider elementProvider
	 *
	 * @covers \Windwalker\Helper\XmlHelper::get
	 */
	public function testGet($element, $attr, $value, $expected)
	{
		$getAttr = XmlHelper::get($element->$attr, $value);

		$this->assertEquals($getAttr, $expected);
	}

	/**
	 * testBoolForTrue
	 *
	 * @param \SimpleXMLElement $element
	 * @param string            $attr
	 *
	 * @return  void
	 *
	 * @dataProvider elementForTrueProvider
	 *
	 * @covers \Windwalker\Helper\XmlHelper::getBool
	 */
	public function testBoolForTrue($element, $attr)
	{
		$testForTrue = XmlHelper::getBool($element->spider, $attr);

		$this->assertTrue($testForTrue);
	}

	/**
	 * testBoolForFalse
	 *
	 * @param \SimpleXMLElement $element
	 * @param string            $attr
	 * @param string            $expected
	 *
	 * @return  void
	 *
	 * @dataProvider elementForFalseProvider
	 *
	 * @covers \Windwalker\Helper\XmlHelper::getBool
	 */
	public function testBoolForFalse($element, $attr, $expected)
	{
		$testForFalse = XmlHelper::getBool($element->batman, $attr);

		$this->assertEquals($testForFalse, $expected);
	}

	/**
	 * testGetFalse
	 *
	 * @param \SimpleXMLElement $element
	 * @param string            $attr
	 *
	 * @return  void
	 *
	 * @dataProvider elementForTrueProvider
	 *
	 * @covers \Windwalker\Helper\XmlHelper::getFalse
	 */
	public function testGetFalse($element, $attr)
	{
		$getFalse = XmlHelper::getFalse($element->spider, $attr);

		$this->assertFalse($getFalse);
	}

	/**
	 * testGetAttributes
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Helper\XmlHelper::getAttributes
	 */
	public function testGetAttributes()
	{
		$document = '<xml>
					   <nikon type="camera" price="40000" store="20" color="black">D850</nikon>
					</xml>';

		$element = new \SimpleXMLElement($document);

		$getAttributes = XmlHelper::getAttributes($element->nikon);

		$result = array(
			'type' => 'camera',
			'price' => '40000',
			'store' => '20',
			'color' => 'black'
		);

		$this->assertEquals($getAttributes, $result);
	}

	/**
	 * elementProvider
	 *
	 * @return  array
	 */
	public function elementProvider()
	{
		$document = '<xml>
					   <nikon type="camera" price="40000" store="20" color="black">D850</nikon>
					   <cannon type="camera" store="21" color="white" />
					   <casio type="watch" store="21" color="silver" />
					</xml>';

		$data = new \SimpleXMLElement($document);

		return array(
				array($data, 'nikon', 'type', 'camera'),
				array($data, 'cannon', 'store', '21'),
				array($data, 'casio', 'color', 'silver'),
				array($data, 'cannon', 'price', null),
				array($data, 'romeo', 'type', null),
		);
	}

	/**
	 * elementForTrueProvider
	 *
	 * @return  array
	 */
	public function elementForTrueProvider()
	{
		$document = '<xml>
						<spider man="1" magic="true" great="yes"></spider>
					</xml>';

		$data = new \SimpleXMLElement($document);

		return array(
			array($data, 'man'),
			array($data, 'magic'),
			array($data, 'great'),
		);
	}

	/**
	 * elementForFalseProvider
	 *
	 * @return  array
	 */
	public function elementForFalseProvider()
	{
		$document = '<xml>
					   <batman married="no" fly="false" name="wayne" boss="disabled" parents="none" peace="0"></batman>
					</xml>';

		$data = new \SimpleXMLElement($document);

		return array(
			array($data, 'married', false),
			array($data, 'fly', false),
			array($data, 'name', true),
			array($data, 'boss', false),
			array($data, 'parents', false),
			array($data, 'peace', false),
		);
	}
}
