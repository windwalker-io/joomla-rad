<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Script;

use Windwalker\Script\CoreScript;

/**
 * Test class of \Windwalker\Script\CoreScript
 *
 * @since {DEPLOY_VERSION}
 */
class CoreScriptTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Property doc.
	 *
	 * @var  \JDocumentHTML
	 */
	protected $doc;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$doc = \JFactory::getDocument();
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
	 * Method to test underscore().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\CoreScript::underscore
	 */
	public function testUnderscore()
	{
		CoreScript::underscore();

		$doc = \JFactory::getDocument();

		show($doc->_script, $doc->_scripts);
	}

	/**
	 * Method to test underscoreString().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\CoreScript::underscoreString
	 * @TODO   Implement testUnderscoreString().
	 */
	public function testUnderscoreString()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test requireJS().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\CoreScript::requireJS
	 * @TODO   Implement testRequireJS().
	 */
	public function testRequireJS()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test backbone().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\CoreScript::backbone
	 * @TODO   Implement testBackbone().
	 */
	public function testBackbone()
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
	 * @covers Windwalker\Script\CoreScript::windwalker
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
	 * Method to test getAsset().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\CoreScript::getAsset
	 * @TODO   Implement testGetAsset().
	 */
	public function testGetAsset()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test setAsset().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\CoreScript::setAsset
	 * @TODO   Implement testSetAsset().
	 */
	public function testSetAsset()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}