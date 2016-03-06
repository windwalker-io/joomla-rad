<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Bundle;

use Windwalker\DI\Container;
use Joomla\Registry\Registry;
use Windwalker\Test\Application\TestApplication;
use Windwalker\Test\TestHelper;

/**
 * Test class of Windwalker\Controller\Controller
 *
 * @since 2.0
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * setUpBeforeClass
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		\JLoader::registerPrefix('Stub',  __DIR__ . '/Stub');
		\JLoader::register('StubController',  __DIR__ . '/Stub/controller.php');
	}

	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	public function setUp()
	{
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
	}

	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::__construct
	 */
	public function test__construct()
	{
		$controller = new \StubController;

		$this->assertAttributeSame(\JFactory::getApplication(), 'app', $controller);
		$this->assertAttributeSame(\JFactory::getApplication()->input, 'input', $controller);
		$this->assertNull(TestHelper::getValue($controller, 'prefix'));
		$this->assertNull(TestHelper::getValue($controller, 'option'));
		$this->assertNull(TestHelper::getValue($controller, 'name'));
		$this->assertEquals('', TestHelper::getValue($controller, 'task'));

		$input = new \JInput;
		$app = new \JApplicationSite(null, new Registry(array('session' => false)));
		$config = array(
			'prefix' => 'prefix',
			'option' => 'option',
			'name'   => 'test',
			'task'   => 'task',
		);
		$controller = new \StubController($input, $app, $config);

		$this->assertSame($app,   TestHelper::getValue($controller, 'app'));
		$this->assertSame($input, TestHelper::getValue($controller, 'input'));

		$this->assertEquals($config['prefix'], TestHelper::getValue($controller, 'prefix'));
		$this->assertEquals($config['option'], TestHelper::getValue($controller, 'option'));
		$this->assertEquals($config['name'],   TestHelper::getValue($controller, 'name'));
		$this->assertEquals($config['task'],   TestHelper::getValue($controller, 'task'));

		$controller = new \StubControllerFoo;

		$this->assertEquals('Stub',     TestHelper::getValue($controller, 'prefix'));
		$this->assertEquals('com_test', TestHelper::getValue($controller, 'option'));
		$this->assertEquals('Foo',      TestHelper::getValue($controller, 'name'));
		$this->assertEquals('default',  TestHelper::getValue($controller, 'task'));
	}

	/**
	 * Method to test execute().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::execute
	 */
	public function testExecute()
	{
		$controller = $this->getMockBuilder('Windwalker\Controller\Controller')
			->disableOriginalConstructor()
			->setMethods(array('doExecute', 'prepareExecute', 'postExecute'))
			->getMock();

		$controller->expects($this->once())
			->method('doExecute');
		$controller->expects($this->once())
			->method('prepareExecute');
		$controller->expects($this->once())
			->method('postExecute')
			->will($this->returnValue('postExecute'));

		$this->assertEquals('postExecute', $controller->execute());

		$controller = new \StubController;

		$this->assertEquals('controller test', $controller->execute());
	}

	/**
	 * Method to test fetch().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::fetch
	 */
	public function testFetch()
	{
		$controller = new \StubControllerFoo;
		$container = $controller->getContainer();

		// ControllerResolver
		$resolverClass = '\\Windwalker\\Controller\\Resolver\\ControllerResolver';

		$container->alias('controller.resolver', $resolverClass)
			->share(
				$resolverClass,
				function(Container $container) use($resolverClass)
				{
					return new $resolverClass($container->get('app'), $container);
				}
			);

		$this->assertEquals('bar controller test', $controller->fetch('Stub', 'Bar'));
	}

	/**
	 * testGetReflection
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Component\Component::getReflection
	 */
	public function testGetReflection()
	{
		$controller = new \StubController;

		$this->assertEquals(new \ReflectionClass($controller), $controller->getReflection());
	}

	/**
	 * Method to test getName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::getName
	 */
	public function testGetName()
	{
		// Controller with default name property
		$controller = new \StubControllerFoo;

		$this->assertEquals('Foo', $controller->getName());

		// Controller with empty name property
		$controller = new \StubControllerBar;

		$this->assertEquals('Bar', $controller->getName());
	}

	/**
	 * Method to test checkToken().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::checkToken
	 */
	public function testCheckToken()
	{
		$this->markTestSkipped(
			'This methos contains jexit() so we don\'t test it.'
		);
	}

	/**
	 * Method to test getModel().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::getModel
	 */
	public function testGetModel()
	{
		$controller = new \StubControllerFoo;

		$model = $controller->getModel();

		$this->assertInstanceOf('StubModelFoo', $model);
		$this->assertTrue($controller->getContainer()->exists('model.foo'));
		$this->assertEquals(strtolower($controller->getName()), $model->getState()->get('name'));
		$this->assertEquals(strtolower(TestHelper::getValue($controller, 'option')), $model->getState()->get('option'));
		$this->assertEquals(strtolower($controller->getPrefix()), $model->getState()->get('prefix'));

		$model = $controller->getModel('Bar');

		$this->assertInstanceOf('StubModelBar', $model);
		$this->assertTrue($controller->getContainer()->exists('model.bar'));

		$model = $controller->getModel('bar');

		$this->assertInstanceOf('StubModelBar', $model);
		$this->assertTrue($controller->getContainer()->exists('model.bar'));

		$model = $controller->getModel('not', 'exists');

		$this->assertInstanceOf('Windwalker\Model\Model', $model);
		$this->assertTrue($controller->getContainer()->exists('model.not'));
	}

	/**
	 * Method to test getContainer().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::getContainer
	 */
	public function testGetContainer()
	{
		// Test case #1: Get container with empty "container" property
		$controller = new \StubControllerFoo;
		$option = TestHelper::getValue($controller, 'option');

		$this->assertSame(Container::getInstance($option), $controller->getContainer());

		// Test case #2: Get container with an assigned "container" property
		$controller = new \StubControllerFoo;
		$container = new Container();

		$controller->setContainer($container);

		$this->assertSame($container, $controller->getContainer());
	}

	/**
	 * Method to test setMessage().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Controller\Controller::setMessage
	 */
	public function testSetMessage()
	{
		$message = 'test messages...';
		$type = 'warning';
		$controller = new \StubControllerFoo;
		/** @var TestApplication $app */
		$app = $controller->getApplication();

		$controller->setMessage($message, $type);

		$expectMessages = array(
			array('message' => $message, 'type' => strtolower($type)),
		);

		$this->assertEquals($expectMessages, $app->messages);
	}
}
