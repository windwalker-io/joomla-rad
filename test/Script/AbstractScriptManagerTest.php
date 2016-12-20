<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Script;

use Windwalker\Asset\AssetManager;
use Windwalker\Script\AbstractScriptManager;
use Windwalker\Script\CoreScript;
use Windwalker\Test\Script\Stub\StubScript;

/**
 * Test class of \Windwalker\Script\AbstractScriptManager
 *
 * @since 2.1
 */
class AbstractScriptManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test getAsset().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\AbstractScriptManager::getAsset
	 * @covers \Windwalker\Script\AbstractScriptManager::setAsset
	 */
	public function testGetAndSetAsset()
	{
		$this->assertInstanceOf('Windwalker\Asset\AssetManager', AbstractScriptManager::getAsset());

		AbstractScriptManager::setAsset('test', $asset = new AssetManager);

		$this->assertSame($asset, AbstractScriptManager::getAsset('test'));
	}

	/**
	 * Method to test reset().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Script\AbstractScriptManager::reset
	 */
	public function testReset()
	{
		CoreScript::windwalker();

		$inited = $this->readAttribute('Windwalker\Script\AbstractScriptManager', 'inited');

		$this->assertEquals(
			array('a03e9ce134099d2bd410bdc53e8abb7d3f95c397' => true),
			$inited['Windwalker\Script\CoreScript']['Windwalker\Script\CoreScript::windwalker']
		);

		StubScript::reset();

		$inited = $this->readAttribute('Windwalker\Script\AbstractScriptManager', 'inited');

		$this->assertEquals(
			array('a03e9ce134099d2bd410bdc53e8abb7d3f95c397' => true),
			$inited['Windwalker\Script\CoreScript']['Windwalker\Script\CoreScript::windwalker']
		);

		AbstractScriptManager::reset();

		$inited = $this->readAttribute('Windwalker\Script\AbstractScriptManager', 'inited');

		$this->assertEmpty($inited);

		CoreScript::windwalker();

		StubScript::reset(true);

		$inited = $this->readAttribute('Windwalker\Script\AbstractScriptManager', 'inited');

		$this->assertEmpty($inited);
	}
}