<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Component;

use Joomla\Registry\Registry;
use Windwalker\DI\Container;
use Windwalker\Provider\SystemProvider;
use Windwalker\Test\Component\StubAdmin\StubComponent;
use Windwalker\Test\Application\TestApplication;
use Windwalker\Test\TestCase\AbstractBaseTestCase;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Component\Component
 *
 * @since 2.1
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
				),
				'constants' => false
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
	 * @covers \Windwalker\Component\Component::__construct
	 */
	public function test__construct()
	{
		// Test no constants
		$this->createComponent();
		$this->assertFalse(defined('STUB_ADMIN'));

		// Create with no params
		$component = new StubComponent;

		$this->assertEquals('stub', $component->getName());

		$container = Container::getInstance('com_stub');

		$this->assertSame($container, $component->getContainer());
		$this->assertSame($container->get('app'), $component->getApplication());
		$this->assertSame($container->get('app')->input, $component->getInput());
		$this->assertEquals('com_stub', $component->getOption());
		$this->assertEquals(STUB_ADMIN, $component->getAdminPath());
		$this->assertEquals(STUB_SITE, $component->getSitePath());
		$this->assertEquals(STUB_SELF, $component->getPath());

		// Create with manually set params
		$app = new TestApplication;
		$input = new \JInput;
		$container = new Container;
		$container->registerServiceProvider(new SystemProvider);

		// Mock Event
		$event = $this->getMock('JEventDispatcher', array('trigger'), array('eventName'), 'MockDispatcher');
		$event->expects($this->at(0))
			->method('trigger')
			->with(
				'onComponentBeforeInit'
			);

		$event->expects($this->at(1))
			->method('trigger')
			->with(
				'onComponentAfterInit'
			);

		$container->share('JEventDispatcher', $event)->alias('event.dispatcher', 'JEventDispatcher');

		$component = new StubComponent('flower', $input, $app, $container);

		$this->assertSame($container, $component->getContainer());
		$this->assertSame($app, $component->getApplication());
		$this->assertSame($input, $component->getInput());
		$this->assertEquals('com_flower', $component->getOption());
		$this->assertEquals('flower', $component->getName());

		// Other test
		$this->assertEquals('foo', $input->get('controller'));
		$this->assertEquals('foo', $input->get('task'));

		$paths = TestHelper::getValue('JFormHelper', 'paths');
		$this->assertTrue(in_array(WINDWALKER_SOURCE . '/Form/Fields', $paths['field']));
		$this->assertTrue(in_array(WINDWALKER_SOURCE . '/Form/Forms', $paths['form']));
	}

	/**
	 * Method to test execute().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Component\Component::execute
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
	 * @covers \Windwalker\Component\Component::getActions
	 */
	public function testGetActions()
	{
		$component = $this->createComponent();

		// Get component ACL actions
		$actions = $component->getActions('sakura');

		$this->assertArrayHasKey('windwalker.component.running', $actions->getProperties());

		// Get single item ACL actions
		$actions = $component->getActions('sakura', 3, 0);

		$this->assertArrayHasKey('windwalker.category.running', $actions->getProperties());

		// Get category ACL actions
		$actions = $component->getActions('sakura', 0, 25);

		$this->assertArrayHasKey('windwalker.sakura.running', $actions->getProperties());

		// Get items ACL actions
		$actions = $component->getActions('sakura', true, true);

		$this->assertArrayHasKey('windwalker.sakura.running', $actions->getProperties());
	}

	/**
	 * Method to test getPath().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Component\Component::getPath
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
	 * @covers \Windwalker\Component\Component::getSitePath
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
	 * @covers \Windwalker\Component\Component::getAdminPath
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
