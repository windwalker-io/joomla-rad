<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\Helper\ArrayHelper;

/**
 * Test class of Windwalker\Helper\ArrayHelper
 *
 * @since 2.0
 */
class ArrayHelperTest extends \PHPUnit_Framework_TestCase
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
	 * Method to test repair().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\ArrayHelper::getByPath
	 */
	public function testGetByPath()
	{
		$object                 = new \stdClass;
		$object->Alice          = 'Julia';
		$object->Johnny         = array(
			'David' => 123,
			'Peter' => 'John',
		);
		$object->Vanessa        = new \stdClass;
		$object->Vanessa->Maria = 'Catherine';

		$data = array(
			'Jones'  => array(
				'Sakura' => 223
			),
			'Arthur' => array(
				'Lancelot' => array(
					'Jessica' => $object,
					'Rose'    => array(
						'Taylor' => 323
					)
				)
			)
		);

		// Test null return
		$this->assertEquals(null, ArrayHelper::getByPath($data, ''));
		$this->assertEquals(null, ArrayHelper::getByPath($data, null));

		// Test paths
		$this->assertEquals(223, ArrayHelper::getByPath($data, 'Jones.Sakura'));
		$this->assertEquals(223, ArrayHelper::getByPath($data, 'Jones..Sakura'));
		$this->assertEquals(array('Taylor' => 323), ArrayHelper::getByPath($data, 'Arthur.Lancelot.Rose'));
		$this->assertEquals(323, ArrayHelper::getByPath($data, 'Arthur.Lancelot.Rose.Taylor'));
		$this->assertEquals('Julia', ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Alice'));
		$this->assertEquals(array('David' => 123, 'Peter' => 'John'), ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Johnny'));
		$this->assertEquals(123, ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Johnny.David'));
		$this->assertEquals('John', ArrayHelper::getByPath($data, 'Arthur.Lancelot.Jessica.Johnny.Peter'));
	}

	/**
	 * pivotDataProvider
	 *
	 * @return array
	 */
	public function pivotDataProvider()
	{
		return array(
			array(
				// data
				array(
					'Jones'  => array(123, 223),
					'Arthur' => array('Lancelot', 'Jessica')
				),
				// expected
				array(
					array('Jones' => 123, 'Arthur' => 'Lancelot'),
					array('Jones' => 223, 'Arthur' => 'Jessica'),
				),
			),
		);
	}

	/**
	 * pivotSortDataProvider
	 *
	 * @return array
	 */
	public function pivotSortDataProvider()
	{
		return array(
			array(
				// data
				array(
					array('Jones' => 123, 'Arthur' => 223, 'Lancelot' => 323),
					array('Lancelot' => 'def', 'Arthur' => 'xyz', 'Jones' => 'abc'),
					array('Arthur' => 'foo', 'Lancelot' => 'bar', 'Jones' => 'hello', 'Julia' => 'n/a'),
				),
				// expected
				array(
					'Jones'    => array(123, 'abc', 'hello'),
					'Arthur'   => array(223, 'xyz', 'foo'),
					'Lancelot' => array(323, 'def', 'bar'),
				),
			),
		);
	}

	/**
	 * pivotFromPrefixDataProvider
	 *
	 * @return array
	 */
	public function pivotFromPrefixDataProvider()
	{
		$data = array(
			'prefix' => 'pr_',
			'origin' => array(
				'pr_foo'  => 123,
				'pr_bar'  => 223,
				'pre_bar' => 223,
			),
			'target' => null,
			'expected' => array(
				'foo' => 123,
				'bar' => 223,
			)
		);

		return array(
			array($data['prefix'], $data['origin'],          $data['target'],          $data['expected']),
			array($data['prefix'], (object) $data['origin'], $data['target'],          $data['expected']),
			array($data['prefix'], (object) $data['origin'], (object) $data['target'], (object) $data['expected']),
		);
	}

	/**
	 * pivotToPrefixDataProvider
	 *
	 * @return array
	 */
	public function pivotToPrefixDataProvider()
	{
		$data = array(
			'prefix' => 'pr_',
			'origin' => array(
				'foo' => array(123, 223),
				'bar' => array(323, 423),
			),
			'target' => null,
			'expected' => array(
				'pr_foo'  => array(123, 223),
				'pr_bar'  => array(323, 423),
			)
		);

		return array(
			array($data['prefix'], $data['origin'],          $data['target'],          $data['expected']),
			array($data['prefix'], (object) $data['origin'], $data['target'],          $data['expected']),
			array($data['prefix'], (object) $data['origin'], (object) $data['target'], (object) $data['expected']),
		);
	}

	/**
	 * pivotFromTwoDimensionDataProvider
	 *
	 * @return array
	 */
	public function pivotFromTwoDimensionDataProvider()
	{
		$origin1 = array(
			'Jessica' => array(
				'Alice' => 123,
				'Bruce' => 234,
			),
			'Julia' => array(
				'Jacky' => 345,
			),
		);

		$origin2 = (object) $origin1;
		$origin2->Julia = (object) $origin2->Julia;

		$expected1 = $origin1;
		$expected1['Alice'] = 123;
		$expected1['Bruce'] = 234;
		$expected1['Jacky'] = 345;

		$expected2 = clone $origin2;
		$expected2->Alice = 123;
		$expected2->Bruce = 234;
		$expected2->Jacky = 345;

		return array(
			array($origin1, $expected1),
			array($origin2, $expected2),
		);
	}

	/**
	 * pivotToTwoDimensionDataProvider
	 *
	 * @return array
	 */
	public function pivotToTwoDimensionDataProvider()
	{
		$keys = array('Jessica', 'Bruce');

		$origin1 = array(
			'Jessica' => 789,
			'Alice' => 123,
			'Bruce' => 234,
		);
		$origin2 = (object) $origin1;

		$expected1 = $origin1;
		$expected1['Jessica'] = $origin1;
		$expected1['Bruce'] = $expected1;

		$expected2 = clone $origin2;
		$expected2->Jessica = clone $origin2;
		$expected2->Bruce = clone $expected2;

		return array(
			array($origin1, $keys, $expected1),
			array($origin2, $keys, $expected2),
		);
	}

	/**
	 * Method to test transpose().
	 *
	 * @param array $data
	 * @param array $expected
	 *
	 * @return void
	 *
	 * @dataProvider pivotDataProvider
	 * @covers       \Windwalker\Helper\ArrayHelper::transpose
	 */
	public function testTranspose($data, $expected)
	{
		$this->assertEquals($expected, ArrayHelper::transpose($data));
	}

	/**
	 * Method to test pivotByKey().
	 *
	 * @param array $data
	 * @param array $expected
	 *
	 * @return void
	 *
	 * @dataProvider pivotDataProvider
	 * @covers       \Windwalker\Helper\ArrayHelper::pivotByKey
	 */
	public function testPivotByKey($data, $expected)
	{
		$this->assertEquals($expected, ArrayHelper::pivotByKey($data));
	}

	/**
	 * Method to test pivotBySort().
	 *
	 * @param array $data
	 * @param array $expected
	 *
	 * @return void
	 *
	 * @dataProvider pivotSortDataProvider
	 * @covers       \Windwalker\Helper\ArrayHelper::pivotBySort
	 */
	public function testPivotBySort($data, $expected)
	{
		$this->assertEquals($expected, ArrayHelper::pivotBySort($data));
	}

	/**
	 * Method to test pivotFromPrefix().
	 *
	 * @param string $prefix
	 * @param mixed  $origin
	 * @param mixed  $target
	 * @param mixed  $expected
	 *
	 * @return void
	 *
	 * @dataProvider pivotFromPrefixDataProvider
	 * @covers       \Windwalker\Helper\ArrayHelper::pivotFromPrefix
	 */
	public function testPivotFromPrefix($prefix, $origin, $target, $expected)
	{
		$this->assertEquals($expected, ArrayHelper::pivotFromPrefix($prefix, $origin, $target));
	}

	/**
	 * Method to test pivotToPrefix().
	 *
	 * @param string $prefix
	 * @param mixed  $origin
	 * @param mixed  $target
	 * @param mixed  $expected
	 *
	 * @return void
	 *
	 * @dataProvider pivotToPrefixDataProvider
	 * @covers       \Windwalker\Helper\ArrayHelper::pivotToPrefix
	 */
	public function testPivotToPrefix($prefix, $origin, $target, $expected)
	{
		$this->assertEquals($expected, ArrayHelper::pivotToPrefix($prefix, $origin, $target));
	}

	/**
	 * Method to test pivotFromTwoDimension()
	 *
	 * @param mixed $origin
	 * @param mixed $expected
	 *
	 * @dataProvider pivotFromTwoDimensionDataProvider
	 * @covers       \Windwalker\Helper\ArrayHelper::pivotFromTwoDimension
	 *
	 * @return void
	 */
	public function testPivotFromTwoDimension($origin, $expected)
	{
		$this->assertEquals($expected, ArrayHelper::pivotFromTwoDimension($origin));
	}

	/**
	 * Method to test pivotFromTwoDimension
	 *
	 * @param mixed $origin
	 * @param array $keys
	 * @param mixed $expected
	 *
	 * @dataProvider pivotToTwoDimensionDataProvider
	 * @covers       \Windwalker\Helper\ArrayHelper::pivotFromTwoDimension
	 *
	 * @return void
	 */
	public function testPivotToTwoDimension($origin, $keys, $expected)
	{
		$this->assertEquals($expected, ArrayHelper::pivotToTwoDimension($origin, $keys));
	}

	/**
	 * Method to test query()
	 *
	 * @covers  \Windwalker\Helper\ArrayHelper::query
	 *
	 * @return  void
	 */
	public function testQuery()
	{
		$data = array(
			array(
				'id' => 1,
				'title' => 'Julius Caesar',
				'data' => (object) array('foo' => 'bar'),
			),
			array(
				'id' => 2,
				'title' => 'Macbeth',
				'data' => array(),
			),
			array(
				'id' => 3,
				'title' => 'Othello',
				'data' => 123,
			),
			array(
				'id' => 4,
				'title' => 'Hamlet',
				'data' => true,
			),
		);

		// Test id equals
		$this->assertEquals(array($data[1]), ArrayHelper::query($data, array('id' => 2)));

		// Test strict equals
		$this->assertEquals(array($data[0], $data[2], $data[3]), ArrayHelper::query($data, array('data' => true), false));
		$this->assertEquals(array($data[3]), ArrayHelper::query($data, array('data' => true), true));

		// Test id GT
		$this->assertEquals(array($data[1], $data[2], $data[3]), ArrayHelper::query($data, array('id >' => 1)));

		// Test id GTE
		$this->assertEquals(array($data[1], $data[2], $data[3]), ArrayHelper::query($data, array('id >=' => 2)));

		// Test id LT
		$this->assertEquals(array($data[0], $data[1]), ArrayHelper::query($data, array('id <' => 3)));

		// Test id LTE
		$this->assertEquals(array($data[0], $data[1]), ArrayHelper::query($data, array('id <=' => 2)));

		// Test array equals
		$this->assertEquals(array($data[1]), ArrayHelper::query($data, array('data' => array())));

		// Test object equals
		$object = new \stdClass;
		$object->foo = 'bar';
		$this->assertEquals(array($data[0], $data[3]), ArrayHelper::query($data, array('data' => $object)));

		// Test object strict equals
		$this->assertEquals(array($data[0]), ArrayHelper::query($data, array('data' => $data[0]['data']), true));

		// Test Keep Key
		$this->assertEquals(array(1 => $data[1], 2 => $data[2], 3 => $data[3]), ArrayHelper::query($data, array('id >=' => 2), false, true));
	}

	/**
	 * Method to test setValue
	 *
	 * @covers \Windwalker\Helper\ArrayHelper::setValue
	 *
	 * @return void
	 */
	public function testSetValue()
	{
		$data = array(
			'Archer' => 'Unlimited Blade World',
			'Saber'  => 'Excalibur',
			'Lancer' => 'Gáe Bulg',
			'Rider'  => 'Breaker Gorgon',
		);
		$data2 = (object) $data;

		$newData = ArrayHelper::setValue($data, 'Saber', 'Avalon');

		$this->assertEquals('Avalon', $data['Saber']);
		$this->assertEquals('Avalon', $newData['Saber']);

		$newData = ArrayHelper::setValue($data, 'Archer', 'Unlimited Blade Works');

		$this->assertEquals('Unlimited Blade Works', $data['Archer']);
		$this->assertEquals('Unlimited Blade Works', $newData['Archer']);

		$newData = ArrayHelper::setValue($data, 'Berserker', 'Gold Hand');

		$this->assertEquals('Gold Hand', $data['Berserker']);
		$this->assertEquals('Gold Hand', $newData['Berserker']);

		$newData2 = ArrayHelper::setValue($data2, 'Saber', 'Avalon');

		$this->assertEquals('Avalon', $data2->Saber);
		$this->assertEquals('Avalon', $newData2->Saber);

		$newData2 = ArrayHelper::setValue($data2, 'Archer', 'Unlimited Blade Works');

		$this->assertEquals('Unlimited Blade Works', $data2->Archer);
		$this->assertEquals('Unlimited Blade Works', $newData2->Archer);

		$newData2 = ArrayHelper::setValue($data2, 'Berserker', 'Gold Hand');

		$this->assertEquals('Gold Hand', $data2->Berserker);
		$this->assertEquals('Gold Hand', $newData2->Berserker);
	}

	/**
	 * Method to test getValue
	 *
	 * @covers \Windwalker\Helper\ArrayHelper::getValue
	 *
	 * @return void
	 */
	public function testGetValue()
	{
		$data = array(
			'Caster'    => 'Rule Breaker',
			'Assassin'  => 'Kischur Zelretch',
			'Gilgamesh' => 'Gate Of Babylon',
		);

		$result = ArrayHelper::getValue($data, 'Caster', 'n/a');
		$this->assertEquals('Rule Breaker', $result);

		$result = ArrayHelper::getValue($data, 'Assassin', 'n/a');
		$this->assertEquals('Kischur Zelretch', $result);

		$result = ArrayHelper::getValue($data, 'Gilgamesh2', 'Gate Of Babylon2');
		$this->assertEquals('Gate Of Babylon2', $result);

		$result = ArrayHelper::getValue($data, 'Gilgamesh3');
		$this->assertEquals(null, $result);
	}

	/**
	 * Method to test mapKey
	 *
	 * @covers \Windwalker\Helper\ArrayHelper::mapKey
	 *
	 * @return void
	 */
	public function testMapKey()
	{
		$data = array(
			'top' => 'Captain America',
			'middle' => 'Iron Man',
			'bottom' => 'Thor',
		);
		$data2 = (object) $data;

		$map = array(
			'middle' => 'bottom',
			'bottom' => 'middle',
		);

		$expected = array(
			'top' => 'Captain America',
			'middle' => 'Thor',
			'bottom' => 'Iron Man',
		);
		$expected2 = (object) $expected;

		$result = ArrayHelper::mapKey($data, $map);
		$this->assertEquals($expected, $result);

		$result2 = ArrayHelper::mapKey($data2, $map);
		$this->assertEquals($expected2, $result2);
	}

	/**
	 * Method to test merge
	 *
	 * @covers \Windwalker\Helper\ArrayHelper::merge
	 *
	 * @return void
	 */
	public function testMerge()
	{
		$data1 = array(
			'green'     => 'Hulk',
			'red'       => 'empty',
			'human'     => array(
				'dark'  => 'empty',
				'black' => array(
					'male'      => 'empty',
					'female'    => 'empty',
					'no-gender' => 'empty',
				),
			)
		);
		$data2 = array(
			'ai'        => 'Jarvis',
			'agent'     => 'Phil Coulson',
			'red'       => array(
				'left'  => 'Pepper',
				'right' => 'Iron Man',
			),
			'human'     => array(
				'dark'  => 'Nick Fury',
				'black' => array(
					'female' => 'Black Widow',
					'male'   => 'Loki',
				),
			)
		);

		$expected = array(
			'ai'        => 'Jarvis',
			'agent'     => 'Phil Coulson',
			'green' => 'Hulk',
			'red'       => array(
				'left'  => 'Pepper',
				'right' => 'Iron Man',
			),
			'human'     => array(
				'dark'  => 'Nick Fury',
				'black' => array(
					'male'      => 'Loki',
					'female'    => 'Black Widow',
					'no-gender' => 'empty',
				),
			),
		);

		$expected2 = array(
			'ai'        => 'Jarvis',
			'agent'     => 'Phil Coulson',
			'green' => 'Hulk',
			'red'       => array(
				'left'  => 'Pepper',
				'right' => 'Iron Man',
			),
			'human'     => array(
				'dark'  => 'Nick Fury',
				'black' => array(
					'male'   => 'Loki',
					'female' => 'Black Widow',
				),
			),
		);

		$this->assertEquals($expected, ArrayHelper::merge($data1, $data2));
		$this->assertEquals($expected2, ArrayHelper::merge($data1, $data2, false));
	}
}
