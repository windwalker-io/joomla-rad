<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Script;

use Windwalker\Helper\AssetHelper;
use Windwalker\Script\Module;
use Windwalker\Script\ModuleManager;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Script\Module
 *
 * @since {DEPLOY_VERSION}
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \Windwalker\Script\Module
	 */
	protected $instance;

	/**
	 * Property manager.
	 *
	 * @var  ModuleManager
	 */
	protected $manager;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->manager = new ModuleManager;

		$closure = function (Module $module, AssetHelper $asset)
		{

		};

		$this->instance = new \Windwalker\Script\Module('sakura', $closure, $this->manager);
	}

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
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::__construct
	 */
	public function test__construct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test execute().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::execute
	 */
	public function testExecute()
	{
		$testData = array();

		$closure = function (Module $module, AssetHelper $asset, $foo = null, $bar = null) use (&$testData)
		{
			if (!$module->inited())
			{
				$testData['init'][] = true;

				$asset->addJS('init.js');
			}

			if (!$module->stateInited())
			{
				$testData['state'][] = $foo . ' ' . $bar;

				$asset->internalJS($foo . ' ' . $bar);
			}
		};

		$this->instance->setHandler($closure);

		$asset = $this->getMock('Windwalker\Helper\AssetHelper', array('addJs', 'internalJS'));

		$asset->expects($this->at(0))
			->method('addJs')
			->with('init.js');

		$asset->expects($this->at(1))
			->method('internalJS')
			->with(' ');

		$asset->expects($this->at(2))
			->method('internalJS')
			->with('Arnold ');

		$asset->expects($this->at(3))
			->method('internalJS')
			->with('Arnold T-800');

		// Before execute
		$this->assertArrayNotHasKey('init', $testData);
		$this->assertArrayNotHasKey('state', $testData);

		// First execute
		$this->instance->execute($asset, array());

		$this->assertEquals(array(true), $testData['init']);
		$this->assertCount(1, $testData['init']);

		$this->assertEquals(array(' '), $testData['state']);
		$this->assertCount(1, $testData['state']);

		// Do not init again
		$this->instance->execute($asset, array());

		$this->assertEquals(array(' '), $testData['state']);
		$this->assertCount(1, $testData['state']);

		// State with one argument
		$this->instance->execute($asset, array('Arnold'));

		$this->assertEquals(array(' ', 'Arnold '), $testData['state']);
		$this->assertCount(2, $testData['state']);

		// State with one argument, second executed
		$this->instance->execute($asset, array('Arnold'));

		$this->assertEquals(array(' ', 'Arnold '), $testData['state']);
		$this->assertCount(2, $testData['state']);

		// State with two arguments
		$this->instance->execute($asset, array('Arnold', 'T-800'));

		$this->assertEquals(array(' ', 'Arnold ', 'Arnold T-800'), $testData['state']);
		$this->assertCount(3, $testData['state']);

		// State with two arguments, second executed
		$this->instance->execute($asset, array('Arnold', 'T-800'));

		$this->assertEquals(array(' ', 'Arnold ', 'Arnold T-800'), $testData['state']);
		$this->assertCount(3, $testData['state']);
	}

	/**
	 * Method to test execute().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::execute
	 */
	public function testExecuteWithCustomStateId()
	{
		$testData = array();

		$closure = function (Module $module, AssetHelper $asset, $foo = null, $bar = null) use (&$testData)
		{
			$module->setStateId($foo);

			if (!$module->stateInited())
			{
				$testData['state'][] = $foo . ' ' . $bar;

				$asset->internalJS($foo . ' ' . $bar);
			}
		};

		$this->instance->setHandler($closure);

		$asset = $this->getMock('Windwalker\Helper\AssetHelper', array('addJs', 'internalJS'));

		$asset->expects($this->at(0))
			->method('internalJS')
			->with(' ');

		$asset->expects($this->at(1))
			->method('internalJS')
			->with('John ');

		$asset->expects($this->at(2))
			->method('internalJS')
			->with('Arnold T-800');

		// Before execute
		$this->assertArrayNotHasKey('state', $testData);

		// First execute
		$this->instance->execute($asset, array());

		$this->assertEquals(array(' '), $testData['state']);
		$this->assertCount(1, $testData['state']);

		// Do not init again
		$this->instance->execute($asset, array());

		$this->assertEquals(array(' '), $testData['state']);
		$this->assertCount(1, $testData['state']);

		// State with one argument
		$this->instance->execute($asset, array('John'));

		$this->assertEquals(array(' ', 'John '), $testData['state']);
		$this->assertCount(2, $testData['state']);

		// State with one argument, second executed
		$this->instance->execute($asset, array('John'));

		$this->assertEquals(array(' ', 'John '), $testData['state']);
		$this->assertCount(2, $testData['state']);

		// State with two arguments
		$this->instance->execute($asset, array('Arnold', 'T-800'));

		$this->assertEquals(array(' ', 'John ', 'Arnold T-800'), $testData['state']);
		$this->assertCount(3, $testData['state']);

		// State with two arguments, second executed
		$this->instance->execute($asset, array('Arnold', 'T-1000'));

		$this->assertEquals(array(' ', 'John ', 'Arnold T-800'), $testData['state']);
		$this->assertCount(3, $testData['state']);
	}

	/**
	 * Method to test inited().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::inited
	 */
	public function testInited()
	{
		$this->assertFalse($this->instance->inited());
		$this->assertFalse($this->instance->inited($this->instance->createStateId(array('foo', 'bar'))));

		$inited = array(
			'init' => true,
			$this->instance->createStateId(array('foo', 'bar')) => true
		);

		TestHelper::setValue($this->instance, 'inited', $inited);

		$this->assertTrue($this->instance->inited());
		$this->assertTrue($this->instance->inited($this->instance->createStateId(array('foo', 'bar'))));
		$this->assertFalse($this->instance->inited($this->instance->createStateId(array('foo'))));
	}

	/**
	 * Method to test stateInited().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::stateInited
	 */
	public function testStateInited()
	{
		$this->assertFalse($this->instance->stateInited());
		$this->assertFalse($this->instance->stateInited(array('foo', 'bar')));

		$inited = array(
			'init' => true,
			$this->instance->createStateId(array('foo', 'bar')) => true,
			$this->instance->createStateId(array('flower', 'sakura')) => true,
		);

		TestHelper::setValue($this->instance, 'currentArguments', array('foo', 'bar'));
		TestHelper::setValue($this->instance, 'inited', $inited);

		$this->assertTrue($this->instance->stateInited());
		$this->assertTrue($this->instance->stateInited(array('flower', 'sakura')));
		$this->assertFalse($this->instance->stateInited(array('flower', 'sunflower')));
	}

	/**
	 * Method to test getName().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::getName
	 * @covers Windwalker\Script\Module::setName
	 */
	public function testGetAndSetName()
	{
		$this->assertEquals('sakura', $this->instance->getName());

		$this->instance->setName('foo');

		$this->assertEquals('foo', $this->instance->getName());
	}

	/**
	 * Method to test getHandler().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::getHandler
	 * @covers Windwalker\Script\Module::setHandler
	 */
	public function testGetAndSetHandler()
	{
		$this->instance->setHandler($closure = function() {});

		$this->assertSame($closure, $this->instance->getHandler());
	}

	/**
	 * Method to test getManager().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::getManager
	 */
	public function testGetManager()
	{
		$this->assertSame($this->manager, $this->instance->getManager());
	}

	/**
	 * Method to test setManager().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::setManager
	 * @TODO   Implement testSetManager().
	 */
	public function testSetManager()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test __toString().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\Module::__toString
	 */
	public function test__toString()
	{
		$this->assertEquals('sakura', (string) $this->instance);
	}

	/**
	 * Method to test getParameterID().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\createStateId::genStateId
	 */
	public function testGetParameterID()
	{
		$this->assertEquals(sha1($this->instance->getName() . serialize(array())), $this->instance->createStateId());
		$this->assertEquals(sha1($this->instance->getName() . serialize(array(1, 2, 3))), $this->instance->createStateId(array(1, 2, 3)));
	}
}
