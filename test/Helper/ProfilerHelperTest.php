<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Helper;

use Windwalker\DI\Container;
use Windwalker\Helper\ProfilerHelper;

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
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		$container = Container::getInstance();
		$config = $container->get('joomla.config');

		$config->set('debug', 0);
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
		ProfilerHelper::mark('bar', $namespace);
		ProfilerHelper::mark('foobar', $namespace);

		$app = Container::getInstance()->get('app');
		$buffer = $app->getUserState('windwalker.system.profiler.' . $namespace);

		$regexpPattern = '/^unit-test [0-9]+\.[0-9]{3} seconds \([0-9]+\.[0-9]{3}\); [0-9]+\.[0-9]{2} MB \([0-9]+\.[0-9]{3}\) - %s/';

		// Assert $buffer[0]
		$regexp = sprintf($regexpPattern, 'foo');
		$this->assertTrue((bool) preg_match($regexp, $buffer[0]));

		// Assert $buffer[1]
		$regexp = sprintf($regexpPattern, 'bar');
		$this->assertTrue((bool) preg_match($regexp, $buffer[1]));

		// Assert $buffer[2]
		$regexp = sprintf($regexpPattern, 'foobar');
		$this->assertTrue((bool) preg_match($regexp, $buffer[2]));
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
