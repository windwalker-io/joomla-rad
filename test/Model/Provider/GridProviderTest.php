<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Model\Provider;

use Windwalker\DI\Container;
use Windwalker\Model\Provider\GridProvider;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Model\Provider\GridProvider
 *
 * @since 2.1
 */
class GridProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Provider\GridProvider::__construct
	 */
	public function test__construct()
	{
		$provider = new GridProvider('FooBar');

		$this->assertEquals('foobar', TestHelper::getValue($provider, 'name'));
	}

	/**
	 * Method to test register().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Model\Provider\GridProvider::register
	 */
	public function testRegister()
	{
		$provider = new GridProvider('foo');
		$container = new Container;

		$provider->register($container);

		$this->assertTrue($container->exists('model.foo.helper.query'));
		$this->assertTrue($container->exists('model.foo.filter'));
		$this->assertTrue($container->exists('model.foo.helper.filter'));
		$this->assertTrue($container->exists('model.foo.search'));
		$this->assertTrue($container->exists('model.foo.helper.search'));

		$this->assertInstanceOf('Windwalker\Model\Helper\QueryHelper', $container->get('model.foo.helper.query'));
		$this->assertInstanceOf('Windwalker\Model\Filter\FilterHelper', $container->get('model.foo.helper.filter'));
		$this->assertInstanceOf('Windwalker\Model\Filter\SearchHelper',  $container->get('model.foo.helper.search'));
	}
}
