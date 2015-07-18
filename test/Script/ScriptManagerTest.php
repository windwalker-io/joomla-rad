<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Script;

use Windwalker\Script\ScriptManager;

/**
 * Test class of \Windwalker\Script\ScriptManager
 *
 * @since {DEPLOY_VERSION}
 */
class ScriptManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test getDIKey().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::getDIKey
	 */
	public function testGetDIKey()
	{
		$this->assertEquals('script.manager', ScriptManager::getDIKey());
	}

	/**
	 * Method to test getInstance().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Script\ScriptManager::getInstance
	 */
	public function testGetInstance()
	{
		$this->assertInstanceOf('Windwalker\Script\ModuleManager', ScriptManager::getInstance());
	}
}
