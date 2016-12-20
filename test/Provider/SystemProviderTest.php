<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Provider;

use Windwalker\DI\Container;
use Windwalker\Provider\SystemProvider;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Provider\SystemProvider
 *
 * @since 2.1
 */
class SystemProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Provider\SystemProvider::__construct
	 */
	public function test__construct()
	{
		$provider = new SystemProvider(true);

		$this->assertTrue(TestHelper::getValue($provider, 'isConsole'));

		$provider = new SystemProvider(false);

		$this->assertFalse(TestHelper::getValue($provider, 'isConsole'));

		$provider = new SystemProvider();

		$this->assertFalse(TestHelper::getValue($provider, 'isConsole'));

		$provider = new SystemProvider('foo');

		$this->assertTrue(TestHelper::getValue($provider, 'isConsole'));

		$provider = new SystemProvider(null);

		$this->assertFalse(TestHelper::getValue($provider, 'isConsole'));
	}

	/**
	 * Method to test register().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Provider\SystemProvider::register
	 */
	public function testRegister()
	{
		// Test case #1: Test register method functionality
		$container = new Container;
		$provider = new SystemProvider;

		$provider->register($container);

		$this->assertSame(\JFactory::getConfig(), $container->get('joomla.config'));
		$this->assertSame(\JFactory::getDbo(), $container->get('db'));
		$this->assertSame(\JFactory::getDbo(), $container->get('JDatabaseDriver'));
		$this->assertSame(\JFactory::getLanguage(), $container->get('language'));
		$this->assertSame(\JFactory::getLanguage(), $container->get('JLanguage'));
		$this->assertSame(\JEventDispatcher::getInstance(), $container->get('event.dispatcher'));
		$this->assertSame(\JEventDispatcher::getInstance(), $container->get('JEventDispatcher'));
		$this->assertInstanceOf('SplPriorityQueue', $container->get('SplPriorityQueue'));
		$this->assertInstanceOf('Windwalker\Asset\AssetManager', $container->get('helper.asset'));

		// Test case #2: Test "$container->registerServiceProvider" parts
		$container = $this->getMockBuilder('Windwalker\DI\Container')
			->disableOriginalConstructor()
			->setMethods(array('registerServiceProvider'))
			->getMock();

		$container->expects($this->at(0))
			->method('registerServiceProvider')
			->with($this->isInstanceOf('Windwalker\Provider\WebProvider'));

		$container->expects($this->at(1))
			->method('registerServiceProvider')
			->with($this->isInstanceOf('Windwalker\Provider\CliProvider'));

		$provider = new SystemProvider;

		$provider->register($container);

		$provider = new SystemProvider(true);

		$provider->register($container);
	}

	/**
	 * Method to test loadConfig().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Provider\SystemProvider::loadConfig
	 * @TODO   Implement testLoadConfig().
	 */
	public function testLoadConfig()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
