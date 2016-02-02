<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Asset;

use Windwalker\Asset\AssetManager;
use Windwalker\Test\Joomla\MockHtmlDocument;
use Windwalker\Test\TestHelper;
use Windwalker\Utilities\Queue\PriorityQueue;

/**
 * Test class of \Windwalker\Asset\AssetManager
 *
 * @since {DEPLOY_VERSION}
 */
class AssetManagerTest extends \PHPUnit_Framework_TestCase
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
	 * @covers Windwalker\Asset\AssetManager::__construct
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
	 * @covers Windwalker\Asset\AssetManager::addCSS
	 * @TODO   Implement testAddCSS().
	 */
	public function testAddCSS()
	{
		show($this->instance->getPaths()->toArray());

		$this->instance->addCSS('windwalker.css');

		show($this->doc);die;
	}

	/**
	 * Method to test addJS().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Asset\AssetManager::addJS
	 * @TODO   Implement testAddJS().
	 */
	public function testAddJS()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test internalCSS().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Asset\AssetManager::internalCSS
	 * @TODO   Implement testInternalCSS().
	 */
	public function testInternalCSS()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test internalJS().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Asset\AssetManager::internalJS
	 * @TODO   Implement testInternalJS().
	 */
	public function testInternalJS()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test windwalker().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Asset\AssetManager::windwalker
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
	 * @covers Windwalker\Asset\AssetManager::jquery
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
	 * @covers Windwalker\Asset\AssetManager::jqueryUI
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
	 * @covers Windwalker\Asset\AssetManager::mootools
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
	 * @covers Windwalker\Asset\AssetManager::bootstrap
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
	 * @covers Windwalker\Asset\AssetManager::isis
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
	 * @covers Windwalker\Asset\AssetManager::getMinName
	 * @TODO   Implement testGetMinName().
	 */
	public function testGetMinName()
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
	 * @covers Windwalker\Asset\AssetManager::resetPaths
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
	 * @covers Windwalker\Asset\AssetManager::getContainer
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
	 * @covers Windwalker\Asset\AssetManager::setContainer
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
	 * @covers Windwalker\Asset\AssetManager::getName
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
	 * @covers Windwalker\Asset\AssetManager::setName
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
	 * @covers Windwalker\Asset\AssetManager::getPaths
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
	 * @covers Windwalker\Asset\AssetManager::setPaths
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
	 * @covers Windwalker\Asset\AssetManager::getDoc
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
	 * @covers Windwalker\Asset\AssetManager::setDoc
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
	 * @covers Windwalker\Asset\AssetManager::setSumName
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
	 * @covers Windwalker\Asset\AssetManager::__clone
	 * @TODO   Implement test__clone().
	 */
	public function test__clone()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}