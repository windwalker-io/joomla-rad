<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Event;

use Windwalker\Event\ListenerHelper;

/**
 * Test class of \Windwalker\Event\ListenerHelper
 *
 * @since 2.1
 */
class ListenerHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 */
	public static function setUpBeforeClass()
	{
		\JLoader::registerNamespace('EventStub', __DIR__);
	}

	/**
	 * Method to test registerListeners().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Event\ListenerHelper::registerListeners
	 */
	public function testRegisterListeners()
	{
		$dispatcher = $this->getMockBuilder('JEventDispatcher')
			->disableOriginalConstructor()
			->setMethods(array('attach'))
			->getMock();

		// "EventStub\Listener\Bar\FooListener" will not be loaded
		$dispatcher->expects($this->exactly(4))
			->method('attach');

		ListenerHelper::registerListeners('EventStub', $dispatcher, __DIR__ . '/EventStub/Listener');
	}
}
