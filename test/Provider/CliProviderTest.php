<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Provider;

use Windwalker\Console\IO\IO;
use Windwalker\DI\Container;
use Windwalker\Provider\CliProvider;
use Windwalker\Registry\Registry;

/**
 * Test class of \Windwalker\Provider\CliProvider
 *
 * @since 2.1
 */
class CliProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test register().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Provider\CliProvider::register
	 */
	public function testRegister()
	{
		$container = new Container;
		$provider = new CliProvider;

		$container->set('windwalker.config', new Registry(array('bundle' => array())));

		$provider->register($container);

		$this->assertInstanceOf('Windwalker\Console\Application\Console', $container->get('app'));
		$this->assertInstanceOf('Windwalker\Console\Application\Console', $container->get('Windwalker\Console\Application\Console'));
		$this->assertSame($container->get('app')->getIO(), $container->get('io'));
		$this->assertInstanceOf('Windwalker\Console\IO\IO', $container->get('io'));
		$this->assertInstanceOf('Windwalker\Console\IO\IO', $container->get('input'));
		$this->assertInstanceOf('Windwalker\Console\IO\IO', $container->get('Windwalker\Console\IO\IO'));
	}

	/**
	 * Method to test createConsole().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Provider\CliProvider::createConsole
	 */
	public function testCreateConsole()
	{
		$container = new Container;
		$provider = new CliProvider;

		$container->share('windwalker.config', new Registry(array('bundle' => array())));
		$container->share('io', new IO);

		$this->assertInstanceOf('Windwalker\Console\Application\Console', $provider->createConsole($container));
	}
}
