<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Bundle;

use Windwalker\Console\IO\IO;
use Windwalker\DI\Container;
use Windwalker\Console\Application\Console;
use Windwalker\Registry\Registry;
use Windwalker\Test\Bundle\Stub\StubBundle;
use Windwalker\Test\Bundle\Stub\StubContainer;

/**
 * Test class of Windwalker\Bundle\AbstractBundle
 *
 * @since 2.0
 */
class AbstractBundleTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * testGetContainer
	 *
	 * @return  void
	 */
	public function testGetContainer()
	{
		// Test case #1: get default container
		$bundle = new StubBundle('test');

		$this->assertInstanceOf('Windwalker\DI\Container', $bundle->getContainer());

		// Test case #2: get a given container
		$bundle = new StubBundle('test2');
		$refContainer = new \ReflectionProperty($bundle, 'container');

		$refContainer->setAccessible(true);
		$refContainer->setValue($bundle, new StubContainer);

		$this->assertInstanceOf('Windwalker\Test\Bundle\Stub\StubContainer', $bundle->getContainer());
	}

	/**
	 * testSetContainer
	 *
	 * @return  void
	 */
	public function testSetContainer()
	{
		$bundle = new StubBundle('test');
		$refContainer = new \ReflectionProperty($bundle, 'container');

		$refContainer->setAccessible(true);

		$bundle->setContainer(new StubContainer);

		$this->assertInstanceOf('Windwalker\Test\Bundle\Stub\StubContainer', $refContainer->getValue($bundle));

		$bundle->setContainer(new Container);

		$this->assertInstanceOf('Windwalker\DI\Container', $refContainer->getValue($bundle));
	}

	/**
	 * testGetName
	 *
	 * @return  void
	 */
	public function testGetName()
	{
		$fooStubBundle = new StubBundle('foo');
		$barStubBundle = new StubBundle('bar');

		$this->assertEquals('foo', $fooStubBundle->getName());
		$this->assertEquals('bar', $barStubBundle->getName());
	}

	/**
	 * testRegisterCommands
	 *
	 * @return  void
	 */
	public function testRegisterCommands()
	{
		$config = new Registry(array('bundle' => array()));
		$console = new Console(new IO, $config);

		StubBundle::registerCommands($console);

		// Test case #1: a normal command
		$this->assertInstanceOf('Windwalker\Test\Bundle\Stub\Command\Foo\FooCommand', $console->getCommand('foo'));

		// Test case #2: a command without inherit AbstractCommand class
		$this->assertNull($console->getCommand('bad'));

		// Test case #3: an invalid command
		$this->assertNull($console->getCommand('woo'));
	}
}
