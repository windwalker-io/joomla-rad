<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\DI;

use Windwalker\DI\Container;
use Windwalker\Filesystem\Iterator\ArrayObject;
use Windwalker\Test\DI\Stub\StubContainer;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Property instance.
	 *
	 * @var \Windwalker\DI\Container
	 */
	public $instance;

	/**
	 * setUp
	 *
	 * @return  void
	 */
	public function setUp()
	{
		$this->instance = new Container;
	}

	/**
	 * testGetInstance
	 *
	 * @covers \Windwalker\DI\Container::getInstance()
	 */
	public function testGetInstance()
	{
		$backup = Container::getInstance();

		$stub = new StubContainer(Container::getInstance());

		$this->assertInstanceOf('Joomla\\DI\\Container', $stub::getInstance());
		$this->assertInstanceOf('Joomla\\DI\\Container', $stub::getInstance('foo'));

		// Test parent
		$this->assertSame(StubContainer::getInstance(), StubContainer::getInstance('stub')->getParent());

		// Put backup instance back
		$ref = new \ReflectionProperty('Windwalker\DI\Container', 'instance');
		$ref->setAccessible(true);
		$ref->setValue($backup);
	}

	/**
	 * testSet
	 *
	 * @covers \Windwalker\DI\Container::set()
	 */
	public function testSet()
	{
		$this->instance->set(
			'foo',
			function($container)
			{
				$bar = new \ArrayObject;

				return array(
					'container' => $container,
					'bar' => $bar
				);
			}
		);

		$callbackResult = $this->instance->get('foo');

		$this->assertInstanceOf('Joomla\\DI\\Container', $callbackResult['container']);
		$this->assertInstanceOf('ArrayObject', $callbackResult['bar']);
	}

	/**
	 * testSetAsProtected
	 *
	 * @expectedException \OutOfBoundsException
	 *
	 * @covers \Windwalker\DI\Container::set()
	 */
	public function testSetAsProtected()
	{
		$this->instance->set(
			'foo',
			function()
			{
				return new \ArrayObject;
			},
			false,
			true
		);

		// Override it, should not be working
		$this->instance->set(
			'foo',
			function()
			{
				return new \ArrayIterator;
			}
		);

		$this->assertInstanceOf('ArrayObject', $this->instance->get('foo'));
	}

	/**
	 * testSetAsShared
	 *
	 * @covers \Windwalker\DI\Container::set()
	 */
	public function testSetAsShared()
	{
		// Not shared
		$this->instance->set(
			'bar',
			function()
			{
				return new \ArrayObject;
			},
			false,
			false
		);

		// Object will be re-initiate, therefore we assertNotSame here
		$this->assertNotSame($this->instance->get('bar'), $this->instance->get('bar'));
		$this->assertInstanceOf('ArrayObject', $this->instance->get('bar'));

		// Shared
		$this->instance->set(
			'foo',
			function()
			{
				return new \ArrayIterator;
			},
			true,
			false
		);

		// Object will be stored into instance, therefore we assertSame here
		$this->assertSame($this->instance->get('foo'), $this->instance->get('foo'));
		$this->assertInstanceOf('ArrayIterator', $this->instance->get('foo'));
	}

	/**
	 * testDump
	 *
	 * @covers \Windwalker\DI\Container::dump()
	 */
	public function testDump()
	{
		// Not shared
		$this->instance->set(
			'foo',
			function()
			{
				return new \ArrayObject;
			},
			false,
			false
		);

		$this->instance->get('foo');
		$dumpResult = $this->instance->dump();

		$this->assertEquals(0, count($dumpResult['data']));

		// Shared
		$this->instance->set(
			'bar',
			function()
			{
				return new \ArrayObject;
			},
			true,
			false
		);

		$this->instance->get('bar');
		$dumpResult = $this->instance->dump();

		$this->assertEquals(1, count($dumpResult['data']));
	}
}
