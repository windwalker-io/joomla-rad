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
use Windwalker\Script\ScriptManager;
use Windwalker\Test\DI\TestContainerHelper;

/**
 * Test class of \Windwalker\Script\ScriptManager
 *
 * @since {DEPLOY_VERSION}
 */
class ScriptManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Original AssertHelper instances
	 *
	 * @var  AssetHelper[]
	 */
	protected $originalAssetHelpers;

	/**
	 * Original modules array
	 *
	 * @var  callable[]
	 */
	protected $originalModules;

	/**
	 * Original initialised data
	 *
	 * @var  boolean[]
	 */
	protected $originalInitialised;

	/**
	 * Property reflectedAssetHelpers.
	 *
	 * @var \ReflectionProperty
	 */
	protected $reflectedAssetHelpers;

	/**
	 * Property reflectedModules.
	 *
	 * @var \ReflectionProperty
	 */
	protected $reflectedModules;

	/**
	 * Property reflectedInitialized.
	 *
	 * @var \ReflectionProperty
	 */
	protected $reflectedInitialized;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->reflectedAssetHelpers = new \ReflectionProperty('Windwalker\Script\ScriptManager', 'assetHelpers');

		$this->reflectedAssetHelpers->setAccessible(true);

		$this->originalAssetHelpers = $this->reflectedAssetHelpers->getValue();

		$this->reflectedModules = new \ReflectionProperty('Windwalker\Script\ScriptManager', 'modules');

		$this->reflectedModules->setAccessible(true);

		$this->originalModules = $this->reflectedModules->getValue();

		$this->reflectedInitialized = new \ReflectionProperty('Windwalker\Script\ScriptManager', 'initialised');

		$this->reflectedInitialized->setAccessible(true);

		$this->originalInitialised = $this->reflectedInitialized->getValue();

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
		$this->reflectedAssetHelpers->setValue($this->originalAssetHelpers);

		$this->reflectedAssetHelpers->setAccessible(false);

		$this->reflectedModules->setValue($this->originalModules);

		$this->reflectedModules->setAccessible(false);

		$this->reflectedInitialized->setValue($this->originalInitialised);

		$this->reflectedInitialized->setAccessible(false);

		Container::getInstance()->get('app')->clearMessageQueue();
	}

	/**
	 * Method to test getHelper().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::getHelper
	 */
	public function testGetHelperWithCache()
	{
		$assetHelpers = $this->reflectedAssetHelpers->getValue();

		$assetHelpers['com_foo'] = 'check cached instance';

		$this->reflectedAssetHelpers->setValue($assetHelpers);

		$helper = ScriptManager::getHelper('com_foo');

		$this->assertEquals('check cached instance', $helper);
	}

	/**
	 * Method to test getHelper().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::getHelper
	 */
	public function testGetHelperWithoutCache()
	{
		$helper = ScriptManager::getHelper('com_bar');

		$this->assertInstanceOf('Windwalker\Helper\AssetHelper', $helper);

		$assetHelpers = $this->reflectedAssetHelpers->getValue();

		$this->assertArrayHasKey('com_bar', $assetHelpers);
		$this->assertSame($helper, $assetHelpers['com_bar']);

		// Clean Container child instance
		TestContainerHelper::deleteChildInstance('com_bar');
	}

	/**
	 * Method to test setModule().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::setModule
	 */
	public function testSetModule()
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

		ScriptManager::setModule('foo', $handler);

		$modules = $this->reflectedModules->getValue();

		$this->assertArrayHasKey('foo', $modules);
		$this->assertSame($handler, $modules['foo']);
	}

	/**
	 * Method to test load().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::load
	 */
	public function testLoad()
	{
		$app = Container::getInstance()->get('app');

		// Test case #1: test none-exists module name
		$result = ScriptManager::load('Module-Not-Exists');
		$expectedMessages = array(array('message' => 'Asset module: module-not-exists not found.', 'type' => 'message'));

		$this->assertFalse($result);
		$this->assertEquals($expectedMessages, $app->getMessageQueue());

		Container::getInstance()->get('app')->clearMessageQueue();

		// Test case #2: test not-callable module name
		ScriptManager::setModule('not-callable', 'function');

		$result = ScriptManager::load('not-callable');
		$expectedMessages = array(array('message' => 'Asset module: not-callable is not callable.', 'type' => 'message'));

		$this->assertFalse($result);
		$this->assertEquals($expectedMessages, $app->getMessageQueue());

		Container::getInstance()->get('app')->clearMessageQueue();

		// Test case #3: test a module name that is initialized
		$testData = 'Test Data';

		ScriptManager::setModule('initialized-module', function() use (&$testData) { $testData = 'Modified Test Data'; });
		$this->reflectedInitialized->setValue(array('initialized-module' => true));

		$result = ScriptManager::load('initialized-module');

		$this->assertTrue($result);
		$this->assertEquals('Test Data', $testData);

		// Test case #4: test a module name that is not initialized
		$testData = 'Test Data';
		$phpunit = $this;

		ScriptManager::setModule('initialized-module', function($name, $helper) use (&$testData, $phpunit) {
			$testData = 'Modified Test Data';

			$phpunit->assertEquals('initialized-module', $name);
			$phpunit->assertInstanceOf('Windwalker\Helper\AssetHelper', $helper);
		});
		$this->reflectedInitialized->setValue(array());

		$result = ScriptManager::load('initialized-module');
		$initialized = $this->reflectedInitialized->getValue();

		$this->assertTrue($result);
		$this->assertEquals('Modified Test Data', $testData);
		$this->assertArrayHasKey('initialized-module', $initialized);
		$this->assertTrue($initialized['initialized-module']);
	}

	/**
	 * Method to test requireJS().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::requireJS
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

		$helpers = $this->reflectedAssetHelpers->getValue();

		$helpers['windwalker'] = $assetHelper;

		$this->reflectedAssetHelpers->setValue($helpers);

		ScriptManager::requireJS();

		// Check `$assetHelper` is invoked only once
		ScriptManager::requireJS();
	}

	/**
	 * Method to test underscore().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::underscore
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

		$helpers = $this->reflectedAssetHelpers->getValue();

		$helpers['windwalker'] = $assetHelper;

		$this->reflectedAssetHelpers->setValue($helpers);

		ScriptManager::underscore();

		// Check `$assetHelper` is invoked only once
		ScriptManager::underscore();
	}

	/**
	 * Method to test underscore() with parameter `$noConflict = false`.
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::underscore
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

		$helpers = $this->reflectedAssetHelpers->getValue();

		$helpers['windwalker'] = $assetHelper;

		$this->reflectedAssetHelpers->setValue($helpers);

		ScriptManager::underscore(false);

		// Check `$assetHelper` is invoked only once
		ScriptManager::underscore(false);
	}

	/**
	 * Method to test backbone().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::backbone
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

		$helpers = $this->reflectedAssetHelpers->getValue();

		$helpers['windwalker'] = $assetHelper;

		$this->reflectedAssetHelpers->setValue($helpers);

		ScriptManager::backbone();

		// Check `$assetHelper` is invoked only once
		ScriptManager::backbone();
	}

	/**
	 * Method to test backbone() with parameter `$noConflict = true`.
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::backbone
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

		$helpers = $this->reflectedAssetHelpers->getValue();

		$helpers['windwalker'] = $assetHelper;

		$this->reflectedAssetHelpers->setValue($helpers);

		ScriptManager::backbone(true);

		// Check `$assetHelper` is invoked only once
		ScriptManager::backbone(true);
	}

	/**
	 * Method to test windwalker().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::windwalker
	 */
	public function testWindwalker()
	{
		$assetHelper = $this->getMockBuilder('Windwalker\Helper\AssetHelper')
			->disableOriginalConstructor()
			->setMethods(array('windwalker', 'addCSS', 'addJS'))
			->getMock();

		$assetHelper->expects($this->once())
			->method('windwalker');

		$helpers = $this->reflectedAssetHelpers->getValue();

		$helpers['windwalker'] = $assetHelper;

		$this->reflectedAssetHelpers->setValue($helpers);

		ScriptManager::windwalker();

		// Check `$assetHelper` is invoked only once
		ScriptManager::windwalker();
	}

	/**
	 * Method to test __callStatic().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::__callStatic
	 */
	public function test__callStatic()
	{
		ScriptManager::setModule('foo', function() {});
		ScriptManager::setModule('bar', function() {});

		$this->assertFalse(ScriptManager::fooBar());
		$this->assertFalse(ScriptManager::loadFooBar());
		$this->assertTrue(ScriptManager::loadFoo());
		$this->assertTrue(ScriptManager::loadBar());
	}
}
