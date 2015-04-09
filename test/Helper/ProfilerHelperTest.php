<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\DI\Container;
use Windwalker\Helper\ProfilerHelper;
use Windwalker\Test\DI\ContainerHelper;

/**
 * Class MockApplication
 */
class MockApplication
{
	/**
	 * Property userState.
	 *
	 * @var  \JRegistry
	 */
	public $userState;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->userState = new \JRegistry;
	}

	/**
	 * Gets a user state.
	 *
	 * @param   string  $key      The path of the state.
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  The user state or null.
	 */
	public function getUserState($key, $default = null)
	{
		return $this->userState->get($key, $default);
	}

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param   string  $key    The path of the state.
	 * @param   string  $value  The value of the variable.
	 *
	 * @return  mixed  The previous state, if one existed.
	 */
	public function setUserState($key, $value)
	{
		return $this->userState->set($key, $value);
	}
}

/**
 * Test class of Windwalker\Helper\ProfilerHelper
 *
 * @since 2.0
 */
class ProfilerHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$container = Container::getInstance();
		$config = $container->get('joomla.config');

		$config->set('debug', 1);

		ContainerHelper::setApplication(new MockApplication);
	}

	/**
	 * Method to test mark().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\ProfilerHelper::mark
	 */
	public function testMark()
	{
		$namespace = 'unit-test';

		ProfilerHelper::mark('foo', $namespace);

		usleep(200000);

		ProfilerHelper::mark('bar', $namespace);

		usleep(300000);

		ProfilerHelper::mark('foobar', $namespace);

		$app = Container::getInstance()->get('app');
		$buffer = $app->getUserState('windwalker.system.profiler.' . $namespace);

		// Assert $buffer[0]
		$this->assertSame('unit-test 0.000 seconds (0.000);', substr($buffer[0], 0, 32));
		$this->assertSame(' - foo', substr($buffer[0], -6));

		// Assert $buffer[1]
		$this->assertSame('unit-test 0.2', substr($buffer[1], 0, 13));
		$this->assertSame('seconds (0.2', substr($buffer[1], 16, 12));
		$this->assertSame(' - bar', substr($buffer[1], -6));

		// Assert $buffer[2]
		$this->assertSame('unit-test 0.5', substr($buffer[2], 0, 13));
		$this->assertSame('seconds (0.3', substr($buffer[2], 16, 12));
		$this->assertSame(' - foobar', substr($buffer[2], -9));
	}

	/**
	 * Method to test render().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Helper\ProfilerHelper::render
	 */
	public function testRender()
	{
		$namespace = 'unit-test';
		$buffer = array('foo', 'bar', 'foobar');
		$expected = "<pre><h3>WindWalker Debug [namespace: unit-test]: </h3>foo\n<br />\nbar\n<br />\nfoobar</pre>";

		$mockProfiler = $this->getMockBuilder('JProfiler')
			->setConstructorArgs(array($namespace))
			->getMock();

		/** @var \JProfiler $mockProfiler */
		$mockProfiler->method('getBuffer')
			->willReturn($buffer);

		// Set mocked profiler instance
		ProfilerHelper::setProfiler($namespace, $mockProfiler);

		$this->assertSame($expected, ProfilerHelper::render($namespace, true));

		ob_start();
		$return = ProfilerHelper::render($namespace);
		$actual = ob_get_contents();
		ob_end_clean();

		$this->assertSame($expected, $actual);
		$this->assertSame('', $return);
	}
}
