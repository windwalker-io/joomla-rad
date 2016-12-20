<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\View\Helper;

use Windwalker\Test\TestHelper;
use Joomla\Registry\Registry;
use Windwalker\Data\Data;
use Windwalker\View\Helper\ToolbarHelper;

/**
 * Test class of \Windwalker\View\Helper\ToolbarHelper
 *
 * @since 2.1
 */
class ToolbarHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Property instance.
	 *
	 * @var toolbarHelper
	 */
	protected $instance;

	/**
	 * setUp
	 *
	 * @return  void
	 */
	protected function setUp()
	{
	}

	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Helper\toolHelper::__construct
	 */
	public function test__construct()
	{
		$data = new Data();

		$config = new Registry(
			array(
				'access' => true
			)
		);

		$data->state = new Registry();

		$buttonSet = array(
			'button1' => 'foo',
			'button2' => 'bar',
			'button3' => 'waa',
		);

		$toolbar = new ToolbarHelper($data, $buttonSet, $config);

		$this->assertSame($buttonSet, $this->readAttribute($toolbar, 'buttonSet'));

		$access = $this->readAttribute($toolbar, 'access');

		$this->assertEquals(true, $access->get(0));
	}

	/**
	 * testRegister
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::register
	 */
	public function testRegister()
	{
		$data = new Data();

		$config = new Registry(
			array(
				'access' => true
			)
		);

		$data->state = new Registry();

		$buttonSet = array();

		$toolbar = new ToolbarHelper($data, $buttonSet, $config);

		$button = 'foo';

		$callbackVar = 0;

		$value['handler'] = function($arg1, $arg2) use(&$callbackVar)
		{
			$callbackVar = $arg1 * $arg2;
		};

		$value['args'] = array(3, 4);

		$toolbar->register($button, $value);

		$this->assertEquals(12, $callbackVar);
	}

	/**
	 * testRegisterButtons
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::registerButtons
	 */
	public function testRegisterButtons()
	{
		$this->markTestIncomplete('There is no return var');
	}

	/**
	 * testCustom
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::custom
	 */
	public function testCustom()
	{
		$this->markTestIncomplete('There is no return var');
	}

	/**
	 * testDeleteList
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::deleteList
	 */
	public function testDeleteList()
	{
		$this->markTestIncomplete('There is no return var');
	}

	/**
	 * testDuplicate
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::duplicate
	 */
	public function testDuplicate()
	{
		$this->markTestIncomplete('There is no return var');
	}

	/**
	 * testModal
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::modal
	 */
	public function testModal()
	{
		$this->markTestIncomplete('There is no return var');
	}

	/**
	 * testPreferences
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::preferences
	 */
	public function testPreferences()
	{
		$this->markTestIncomplete('There is no return var');
	}

	/**
	 * testLink
	 *
	 * @return  void
	 *
	 * @covers \Windwalker/View/Helper/toolbarHelper::link
	 */
	public function testLink()
	{
		$this->markTestIncomplete('There is no return var');
	}
}
