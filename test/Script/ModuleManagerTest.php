<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Script;

use Windwalker\DI\Container;
use Windwalker\Helper\AssetHelper;
use Windwalker\Script\Module;
use Windwalker\Script\ModuleManager;
use Windwalker\Script\ScriptManager;
use Windwalker\Test\DI\TestContainerHelper;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Script\ScriptManager
 *
 * @since {DEPLOY_VERSION}
 */
class ModuleManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Property instance.
	 *
	 * @var  ModuleManager
	 */
	protected $instance;

	/**
	 * Property reflectedAssetHelpers.
	 *
	 * @var  \ReflectionProperty
	 */
	protected $reflectedAssetHelpers;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->instance = new ModuleManager;

		Container::getInstance()->get('app')->clearMessageQueue();
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
		Container::getInstance()->get('app')->clearMessageQueue();
	}

	/**
	 * Method to test getHelper().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::getHelper
	 */
	public function testGetHelperWithCache()
	{
		$assetHelpers = TestHelper::getValue($this->instance, 'assetHelpers');

		$assetHelpers['com_foo'] = 'check cached instance';

		TestHelper::setValue($this->instance, 'assetHelpers', $assetHelpers);

		$helper = $this->instance->getHelper('com_foo');

		$this->assertEquals('check cached instance', $helper);
	}

	/**
	 * Method to test getHelper().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::getHelper
	 */
	public function testGetHelperWithoutCache()
	{
		$helper = $this->instance->getHelper('com_bar');

		$this->assertInstanceOf('Windwalker\Helper\AssetHelper', $helper);

		$assetHelpers = TestHelper::getValue($this->instance, 'assetHelpers');

		$this->assertArrayHasKey('com_bar', $assetHelpers);
		$this->assertSame($helper, $assetHelpers['com_bar']);

		// Clean Container child instance
		TestContainerHelper::deleteChildInstance('com_bar');
	}

	/**
	 * Method to test addModule().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::addModule
	 */
	public function testaddModule()
	{
		/**
		 * Handler
		 *
		 * @param   string       $name
		 * @param   AssetHelper  $helper
		 *
		 * @return  string
		 */
		$handler = function ($name, $helper) {};

		$this->instance->addModule('foo', $handler);

		$this->assertInstanceOf('Windwalker\Script\Module', $this->instance->getModule('foo'));
		$this->assertSame($handler, $this->instance->getModule('foo')->getHandler());
	}

	/**
	 * Method to test load().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::load
	 */
	public function testLoad()
	{
		$app = Container::getInstance()->get('app');

		// Test case #1: test none-exists module name
		$result = $this->instance->load('Module-Not-Exists');
		$expectedMessages = array(array('message' => 'Asset module: module-not-exists not found.', 'type' => 'message'));

		$this->assertFalse($result);
		$this->assertEquals($expectedMessages, $app->getMessageQueue());

		Container::getInstance()->get('app')->clearMessageQueue();

		// Test case #2: test a module name that is initialized
		$testData = 'Test Data';

		$this->instance->addModule('initialized-module', function(Module $module, $asset) use (&$testData)
		{
			$testData = 'Modified Test Data';
		});

		TestHelper::setValue($this->instance->getModule('initialized-module'), 'inited', array('init' => true));

		$this->instance->setLegacy(true);

		$result = $this->instance->load('initialized-module');

		$this->assertTrue($result);
		$this->assertEquals('Test Data', $testData);

		$this->instance->setLegacy(false);

		$this->instance->load('initialized-module');

		$this->assertEquals('Modified Test Data', $testData);

		// Test case #3: test a module name that is not initialized
		$testData = 'Test Data';
		$phpunit = $this;

		$this->instance->addModule('initialized-module', function(Module $module, AssetHelper $helper) use (&$testData, $phpunit)
		{
			$testData = 'Modified Test Data';

			$phpunit->assertEquals('initialized-module', $module->__toString());
			$phpunit->assertInstanceOf('Windwalker\Script\Module', $module);
			$phpunit->assertInstanceOf('Windwalker\Helper\AssetHelper', $helper);
			$phpunit->assertSame($this->instance->getHelper(), $helper);
		});

		$result = $this->instance->load('initialized-module');
		$initialized = TestHelper::getValue($this->instance->getModule('initialized-module'), 'inited');

		$this->assertTrue($result);
		$this->assertEquals('Modified Test Data', $testData);
		$this->assertTrue($initialized['init']);
	}

	/**
	 * Method to test requireJS().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::requireJS
	 */
	public function testRequireJS()
	{
		$assetHelper = $this->getMockBuilder('Windwalker\Helper\AssetHelper')
			->disableOriginalConstructor()
			->setMethods(array('addJs'))
			->getMock();

		$assetHelper->expects($this->once())
			->method('addJs')
			->with('require.js');

		TestHelper::getValue($this->instance, 'assetHelpers');

		$helpers['windwalker'] = $assetHelper;

		TestHelper::setValue($this->instance, 'assetHelpers', $helpers);

		$this->instance->requireJS();

		// Check `$assetHelper` is invoked only once
		$this->instance->requireJS();
	}

	/**
	 * Method to test underscore().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::underscore
	 */
	public function testUnderscore()
	{
		$assetHelper = $this->getMockBuilder('Windwalker\Helper\AssetHelper')
			->disableOriginalConstructor()
			->setMethods(array('addJs', 'internalJS'))
			->getMock();

		$assetHelper->expects($this->once())
			->method('addJs')
			->with('underscore.js');

		$assetHelper->expects($this->once())
			->method('internalJS')
			->with(';var underscore = _.noConflict();');

		TestHelper::getValue($this->instance, 'assetHelpers');

		$helpers['windwalker'] = $assetHelper;

		TestHelper::setValue($this->instance, 'assetHelpers', $helpers);

		$this->instance->underscore();

		// Check `$assetHelper` is invoked only once
		$this->instance->underscore();
	}

	/**
	 * Method to test underscore() with parameter `$noConflict = false`.
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::underscore
	 */
	public function testUnderscoreWithoutNoConflict()
	{
		$assetHelper = $this->getMockBuilder('Windwalker\Helper\AssetHelper')
			->disableOriginalConstructor()
			->setMethods(array('addJs', 'internalJS'))
			->getMock();

		$assetHelper->expects($this->once())
			->method('addJs')
			->with('underscore.js');

		$assetHelper->expects($this->never())
			->method('internalJS');

		TestHelper::getValue($this->instance, 'assetHelpers');

		$helpers['windwalker'] = $assetHelper;

		TestHelper::setValue($this->instance, 'assetHelpers', $helpers);

		$this->instance->underscore(false);

		// Check `$assetHelper` is invoked only once
		$this->instance->underscore(false);
	}

	/**
	 * Method to test backbone().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::backbone
	 */
	public function testBackbone()
	{
		$assetHelper = $this->getMockBuilder('Windwalker\Helper\AssetHelper')
			->disableOriginalConstructor()
			->setMethods(array('addJs', 'internalJS'))
			->getMock();

		$assetHelper->expects($this->at(0))
			->method('addJs')
			->with('underscore.js');

		$assetHelper->expects($this->at(2))
			->method('addJs')
			->with('backbone.js');

		$assetHelper->expects($this->once())
			->method('internalJS')
			->with(';var underscore = _.noConflict();');

		TestHelper::getValue($this->instance, 'assetHelpers');

		$helpers['windwalker'] = $assetHelper;

		TestHelper::setValue($this->instance, 'assetHelpers', $helpers);

		$this->instance->backbone();

		// Check `$assetHelper` is invoked only once
		$this->instance->backbone();
	}

	/**
	 * Method to test backbone() with parameter `$noConflict = true`.
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::backbone
	 */
	public function testBackboneWithNoConflict()
	{
		$assetHelper = $this->getMockBuilder('Windwalker\Helper\AssetHelper')
			->disableOriginalConstructor()
			->setMethods(array('addJs', 'internalJS'))
			->getMock();

		$assetHelper->expects($this->at(0))
			->method('addJs')
			->with('underscore.js');

		$assetHelper->expects($this->at(1))
			->method('internalJS')
			->with(';var underscore = _.noConflict();');

		$assetHelper->expects($this->at(2))
			->method('addJs')
			->with('backbone.js');

		$assetHelper->expects($this->at(3))
			->method('internalJS')
			->with(';var backbone = Backbone.noConflict();');

		TestHelper::getValue($this->instance, 'assetHelpers');

		$helpers['windwalker'] = $assetHelper;

		TestHelper::setValue($this->instance, 'assetHelpers', $helpers);

		$this->instance->backbone(true);

		// Check `$assetHelper` is invoked only once
		$this->instance->backbone(true);
	}

	/**
	 * Method to test windwalker().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::windwalker
	 */
	public function testWindwalker()
	{
		$assetHelper = $this->getMockBuilder('Windwalker\Helper\AssetHelper')
			->disableOriginalConstructor()
			->setMethods(array('windwalker', 'addCSS', 'addJS'))
			->getMock();

		$assetHelper->expects($this->once())
			->method('windwalker');

		TestHelper::getValue($this->instance, 'assetHelpers');

		$helpers['windwalker'] = $assetHelper;

		TestHelper::setValue($this->instance, 'assetHelpers', $helpers);

		$this->instance->windwalker();

		// Check `$assetHelper` is invoked only once
		$this->instance->windwalker();
	}

	/**
	 * Method to test __call().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ModuleManager::__call
	 */
	public function test__call()
	{
		$this->instance->addModule('foo', function() {});
		$this->instance->addModule('bar', function() {});

		$this->assertFalse($this->instance->fooBar());
		$this->assertFalse($this->instance->loadFooBar());
		$this->assertTrue($this->instance->loadFoo());
		$this->assertTrue($this->instance->loadBar());
	}
}
