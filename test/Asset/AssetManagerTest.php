<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Asset;

use Windwalker\Asset\AssetManager;
use Windwalker\Test\Joomla\MockHtmlDocument;
use Windwalker\Test\TestCase\AbstractBaseTestCase;
use Windwalker\Test\TestHelper;

/**
 * Test class of \Windwalker\Asset\AssetManager
 *
 * @since 2.1
 */
class AssetManagerTest extends AbstractBaseTestCase
{
	/**
	 * Test instance.
	 *
	 * @var AssetManager
	 */
	protected $instance;

	/**
	 * Property doc.
	 *
	 * @var  MockHtmlDocument
	 */
	protected $doc;

	/**
	 * Property defaultPaths.
	 *
	 * @var  array
	 */
	protected $defaultPaths = array (
		'administrator/components/{name}/asset/{type}',
		'administrator/components/{name}/asset',
		'media/{name}/{type}',
		'media/{name}',
		'media/windwalker/{type}',
		'media/windwalker',
		'libraries/windwalker/resource/asset/{type}',
		'libraries/windwalker/resource/asset',
		'libraries/windwalker/assets',
	);

	/**
	 * setUpBeforeClass
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		TestHelper::setValue('JUri', 'base', array());

		\JFactory::getConfig()->set('live_site', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$paths = new \SplPriorityQueue;
		$paths->insert('libraries/windwalker/resource/asset/{type}', 800);
		$paths->insert('libraries/windwalker/test/Asset/Stub/{type}', 500);
		$paths->insert('media/jui/{type}', 300);
		$paths->insert('media/{name}/{type}', 100);

		$this->instance = new AssetManager('test', $paths);

		$this->instance->setDoc($this->doc = new MockHtmlDocument);

		$this->doc->reset();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}
	
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::__construct
	 */
	public function test__construct()
	{
		// Create with no dependency
		// -------------------------------------------------
		$asset = new AssetManager;

		$this->assertEquals('windwalker', $asset->getName());

		// Test auto registered paths
		$paths = $asset->getPaths()->toArray();

		$this->assertEquals($this->defaultPaths, $paths);

		// Create with dependencies
		// -------------------------------------------------
		$asset = new AssetManager('com_flower', array('foo/bar', 'yoo/baz'));

		$this->assertEquals('com_flower', $asset->getName());
		$paths = $asset->getPaths()->toArray();

		$this->assertEquals(array('foo/bar', 'yoo/baz'), $paths);
	}

	/**
	 * Method to test addCSS().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::addCSS
	 */
	public function testAddCSS()
	{
		$this->instance->addCSS('windwalker.css');

		$expected = $_SERVER['REQUEST_URI'] . '/libraries/windwalker/resource/asset/css/windwalker.css';

		$this->assertEquals($expected, $this->doc->getLastStylesheet());

		$this->instance->addCSS('stub.css');

		$expected = $_SERVER['REQUEST_URI'] . '/libraries/windwalker/test/Asset/Stub/css/stub.css';

		$this->assertEquals($expected, $this->doc->getLastStylesheet());

		$this->instance->addCSS('chosen.css');

		$expected = $_SERVER['REQUEST_URI'] . '/media/jui/css/chosen.css';

		$this->assertEquals($expected, $this->doc->getLastStylesheet());

		$this->instance->addCSS('http://cdn.js/css.css');

		$expected = 'http://cdn.js/css.css';

		$this->assertEquals($expected, $this->doc->getLastStylesheet());

		$this->instance->isDebug(false);
		$this->instance->setSumName('SUM_TEST');

		$this->instance->addCSS('foo.css');
		$expected = $_SERVER['REQUEST_URI'] . '/libraries/windwalker/test/Asset/Stub/css/foo.min.css?windwalkersum';

		$this->assertEquals($expected, $this->doc->getLastStylesheet());
	}

	/**
	 * Method to test addCSS().
	 *
	 * @param  boolean  $debug
	 * @param  string   $file
	 * @param  string   $expected
	 *
	 * @covers       Windwalker\Asset\AssetManager::addCSS
	 *
	 * @dataProvider addAssetMinProvider
	 */
	public function testAddCSSWithMin($debug, $file, $expected)
	{
		$this->instance->isDebug($debug);

		$this->instance->addCSS($file . '.css');

		$expected = str_replace('{type}', 'css', $expected);

		$expected = $_SERVER['REQUEST_URI'] . '/' . $expected . '.css';

		$this->assertEquals($expected, $this->doc->getLastStylesheet());
	}

	/**
	 * addAssetMinProvider
	 *
	 * @return  array
	 */
	public function addAssetMinProvider()
	{
		return array(
			// Debug
			array(
				true,
				'stub',
				'libraries/windwalker/test/Asset/Stub/{type}/stub'
			),
			array(
				true,
				'stub.min',
				'libraries/windwalker/test/Asset/Stub/{type}/stub'
			),
			array(
				true,
				'bar',
				'libraries/windwalker/test/Asset/Stub/{type}/bar.min'
			),
			array(
				true,
				'bar.min',
				'libraries/windwalker/test/Asset/Stub/{type}/bar.min'
			),
			array(
				true,
				'foo',
				'libraries/windwalker/test/Asset/Stub/{type}/foo'
			),
			array(
				true,
				'foo.min',
				'libraries/windwalker/test/Asset/Stub/{type}/foo'
			),

			// No debug
			array(
				false,
				'stub',
				'libraries/windwalker/test/Asset/Stub/{type}/stub'
			),
			array(
				false,
				'stub.min',
				'libraries/windwalker/test/Asset/Stub/{type}/stub'
			),
			array(
				false,
				'bar',
				'libraries/windwalker/test/Asset/Stub/{type}/bar.min'
			),
			array(
				false,
				'bar.min',
				'libraries/windwalker/test/Asset/Stub/{type}/bar.min'
			),
			array(
				false,
				'foo',
				'libraries/windwalker/test/Asset/Stub/{type}/foo.min'
			),
			array(
				false,
				'foo.min',
				'libraries/windwalker/test/Asset/Stub/{type}/foo.min'
			)
		);
	}

	/**
	 * Method to test addJS().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::addJS
	 */
	public function testAddJS()
	{
		$this->instance->addJS('windwalker.js');

		$expected = $_SERVER['REQUEST_URI'] . '/libraries/windwalker/resource/asset/js/windwalker.js';

		$this->assertEquals($expected, $this->doc->getLastScript());

		$this->instance->addJS('stub.js');

		$expected = $_SERVER['REQUEST_URI'] . '/libraries/windwalker/test/Asset/Stub/js/stub.js';

		$this->assertEquals($expected, $this->doc->getLastScript());

		$this->instance->addJS('chosen.jquery.js');

		$expected = $_SERVER['REQUEST_URI'] . '/media/jui/js/chosen.jquery.js';

		$this->assertEquals($expected, $this->doc->getLastScript());

		$this->instance->addJS('http://cdn.js/js.js');

		$expected = 'http://cdn.js/js.js';

		$this->assertEquals($expected, $this->doc->getLastScript());

		$this->instance->isDebug(false);
		$this->instance->setSumName('SUM_TEST');

		$this->instance->addJS('foo.js');
		$expected = $_SERVER['REQUEST_URI'] . '/libraries/windwalker/test/Asset/Stub/js/foo.min.js?windwalkersum';

		$this->assertEquals($expected, $this->doc->getLastScript());
	}

	/**
	 * Method to test addCSS().
	 *
	 * @param  boolean  $debug
	 * @param  string   $file
	 * @param  string   $expected
	 *
	 * @covers       Windwalker\Asset\AssetManager::addCSS
	 *
	 * @dataProvider addAssetMinProvider
	 */
	public function testAddJSWithMin($debug, $file, $expected)
	{
		$this->instance->isDebug($debug);

		$this->instance->addJS($file . '.js');

		$expected = str_replace('{type}', 'js', $expected);

		$expected = $_SERVER['REQUEST_URI'] . '/' . $expected . '.js';

		$this->assertEquals($expected, $this->doc->getLastScript());
	}

	/**
	 * Method to test internalCSS().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::internalCSS
	 */
	public function testInternalCSS()
	{
		$this->instance->internalCSS('#foo {}');
		$this->instance->internalCSS('#bar {}');

		$this->assertStringSafeEquals("\n#foo {}\n\n#bar {}\n", $this->doc->_style['text/css']);
	}

	/**
	 * Method to test internalJS().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::internalJS
	 */
	public function testInternalJS()
	{
		$this->instance->internalJS('var foo');
		$this->instance->internalJS('var bar');

		$this->assertStringDataEquals(";\nvar foo; ;\n\nvar bar;\n", $this->doc->_script['text/javascript']);
	}

	/**
	 * Method to test getJSObject().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::getJSObject
	 */
	public function testGetJSObject()
	{
		$options = array(
			'string' => 'foo',
			'int' => 123,
			'float' => 1.3,
			'array' => array(
				'flower' => 'sakura'
			),
			'object' => (object) array(
				'flower' => 'sakura'
			),
			'function_string' => 'function () {}',
			'function' => '\\function () {}',
		);

		$expected = '{"string":"foo","int":123,"float":1.3,"array":{"flower":"sakura"},"object":{"flower":"sakura"},"function_string":"function () {}","function":function () {}}';

		$this->assertEquals($expected, AssetManager::getJSObject($options));

		$expected = '{string:"foo",int:123,float:1.3,array:{flower:"sakura"},object:{flower:"sakura"},function_string:"function () {}",function:function () {}}';

		$this->assertEquals($expected, AssetManager::getJSObject($options, false));
	}

	/**
	 * Method to test windwalker().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::windwalker
	 * @TODO   Implement testWindwalker().
	 */
	public function testWindwalker()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test jquery().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::jquery
	 * @TODO   Implement testJquery().
	 */
	public function testJquery()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test jqueryUI().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::jqueryUI
	 * @TODO   Implement testJqueryUI().
	 */
	public function testJqueryUI()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test mootools().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::mootools
	 * @TODO   Implement testMootools().
	 */
	public function testMootools()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test bootstrap().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::bootstrap
	 * @TODO   Implement testBootstrap().
	 */
	public function testBootstrap()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test isis().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::isis
	 * @TODO   Implement testIsis().
	 */
	public function testIsis()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getMinName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::getMinName
	 */
	public function testGetMinFile()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test resetPaths().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::resetPaths
	 * @TODO   Implement testResetPaths().
	 */
	public function testResetPaths()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getContainer().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::getContainer
	 * @TODO   Implement testGetContainer().
	 */
	public function testGetContainer()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setContainer().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::setContainer
	 * @TODO   Implement testSetContainer().
	 */
	public function testSetContainer()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::getName
	 * @TODO   Implement testGetName().
	 */
	public function testGetName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::setName
	 * @TODO   Implement testSetName().
	 */
	public function testSetName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getPaths().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::getPaths
	 * @TODO   Implement testGetPaths().
	 */
	public function testGetPaths()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setPaths().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::setPaths
	 * @TODO   Implement testSetPaths().
	 */
	public function testSetPaths()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getDoc().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::getDoc
	 * @TODO   Implement testGetDoc().
	 */
	public function testGetDoc()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setDoc().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::setDoc
	 * @TODO   Implement testSetDoc().
	 */
	public function testSetDoc()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setSumName().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::setSumName
	 * @TODO   Implement testSetSumName().
	 */
	public function testSetSumName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test __clone().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Asset\AssetManager::__clone
	 */
	public function test__clone()
	{
		$asset = clone $this->instance;
		$this->assertNotSame($this->readAttribute($asset, 'paths'), $this->readAttribute($this->instance, 'paths'));
	}
}