<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Component;

use Joomla\Registry\Registry;
use Windwalker\DI\Container;
use Windwalker\Provider\SystemProvider;
use Windwalker\Test\Component\StubAdmin\StubComponent;
use Windwalker\Test\DI\TestContainerHelper;
use Windwalker\Test\Application\ApplicationTest;
use Windwalker\Test\TestCase\AbstractBaseTestCase;

/**
 * Test class of \Windwalker\Component\Component
 *
 * @since {DEPLOY_VERSION}
 */
class RealComponentTest extends AbstractBaseTestCase
{
	/**
	 * Test instance.
	 *
	 * @var StubComponent
	 */
	protected $instance;

	/**
	 * setUpBeforeClass
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		// Prepare Container
		$app = new ApplicationTest;
		$app->input = new \JInput;

		TestContainerHelper::setApplication($app);
	}

	/**
	 * tearDownAfterClass
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		TestContainerHelper::restoreApplication();
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
	}

	/**
	 * createComponent
	 *
	 * @param array $input
	 *
	 * @return  StubComponent
	 */
	protected function createComponent($input = array())
	{
		if (!($input instanceof \JInput))
		{
			$input = new \JInput($input);
		}

		$container = new Container;

		$container->registerServiceProvider(new SystemProvider);

		$container->share('input', $input);

		$config = array(
			'init' => array(
				'path' => array(
					'self' => __DIR__ . '/StubAdmin',
					'site' => __DIR__ . '/StubSite',
					'administrator' => __DIR__ . '/StubAdmin',
				)
			)
		);

		$container->share('com_stub.config', new Registry($config));

		\JLoader::registerPrefix('Stub', __DIR__ . '/StubAdmin');

		return new StubComponent('stub', $input, $container->get('app'), $container);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		// Reset Component Container
		$ref = new \ReflectionProperty('Windwalker\DI\Container', 'children');
		$ref->setAccessible(true);

		$value = $ref->getValue();
		$value['com_stub'] = null;

		$ref->setValue($value);
	}
	
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Component\Component::__construct
	 */
	public function test__construct()
	{
		// Create with no params
		$component = new StubComponent;

		$this->assertEquals('stub', $component->getName());

		$container = Container::getInstance('com_stub');

		$this->assertSame($container, $component->getContainer());
		$this->assertSame($container->get('app'), $component->getApplication());
		$this->assertSame($container->get('app')->input, $component->getInput());
		$this->assertEquals('com_stub', $component->getOption());

		// Create with manually set params
		$app = new ApplicationTest;
		$input = new \JInput;
		$container = new Container;
		$container->registerServiceProvider(new SystemProvider);

		$component = new StubComponent('flower', $input, $app, $container);

		$this->assertSame($container, $component->getContainer());
		$this->assertSame($app, $component->getApplication());
		$this->assertSame($input, $component->getInput());
		$this->assertEquals('com_flower', $component->getOption());
		$this->assertEquals('flower', $component->getName());
	}

	/**
	 * Method to test execute().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Component\Component::execute
	 */
	public function testExecute()
	{
		// Test with view
		$component = $this->createComponent(array('view' => 'sakura'));

		$this->assertStringSafeEquals('Sakura Tmpl default', $component->execute());

		// Test with view and layout
		$component = $this->createComponent(array('view' => 'sakura', 'layout' => 'foo'));

		$this->assertStringSafeEquals('Sakura Tmpl foo', $component->execute());

		// Test with controller task
		$component = $this->createComponent(array('task' => 'sakura.edit.foo', 'data' => 'bar'));

		$this->assertStringSafeEquals('foo controller data: bar', $component->execute());
	}

	/**
	 * Method to test getActions().
	 *
	 * @return void
	 *
	 * @see  StubAdmin/access.xml
	 *
	 * @covers Windwalker\Component\Component::getActions
	 */
	public function testGetActions()
	{
		$actions = $this->createComponent()->getActions('sakura');

		$this->assertArrayHasKey('windwalker.component.running', $actions->getProperties());

		$actions = $this->createComponent()->getActions('sakura', 3, 0);

		$this->assertArrayHasKey('windwalker.category.running', $actions->getProperties());

		$actions = $this->createComponent()->getActions('sakura', 0, 25);

		$this->assertArrayHasKey('windwalker.sakura.running', $actions->getProperties());

		$actions = $this->createComponent()->getActions('sakura', 3, 25);

		$this->assertArrayHasKey('windwalker.sakura.running', $actions->getProperties());
	}

	/**
	 * Method to test getPath().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Component\Component::getPath
	 * @TODO   Implement testGetPath().
	 */
	public function testGetPath()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getSitePath().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Component\Component::getSitePath
	 * @TODO   Implement testGetSitePath().
	 */
	public function testGetSitePath()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getAdminPath().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Component\Component::getAdminPath
	 * @TODO   Implement testGetAdminPath().
	 */
	public function testGetAdminPath()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
