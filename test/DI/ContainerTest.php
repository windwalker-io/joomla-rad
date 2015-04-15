<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\DI;

use Windwalker\DI\Container;
use Windwalker\Test\DI\Stub\StubContainer;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * testGetInstance
	 *
	 * @covers \Windwalker\DI\Container::getInstance()
	 */
	public function testGetInstance()
	{
		$instance = new StubContainer;

		$this->assertInstanceOf('Joomla\\DI\\Container', $instance::getInstance());
		$this->assertInstanceOf('Joomla\\DI\\Container', $instance::getInstance('foo'));
	}

	/**
	 * testSet
	 *
	 * @covers \Windwalker\DI\Container::set()
	 */
	public function testSet()
	{
		$instance = new Container;

		$instance->set(
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

		$this->assertInstanceOf('Joomla\\DI\\Container', $instance->get('foo')['container']);
		$this->assertInstanceOf('ArrayObject', $instance->get('foo')['bar']);
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
		$instance = new Container;

		$instance->set(
			'foo',
			function()
			{
				return new \ArrayObject;
			},
			false,
			true
		);

		$instance->set(
			'foo',
			function()
			{
				return new \ArrayIterator;
			}
		);

		$this->assertInstanceOf('ArrayObject', $instance->get('foo'));
	}
}
