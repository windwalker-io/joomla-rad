<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later. see LICENSE
 */

namespace Windwalker\Test\Windwalker;

use Windwalker\Console\IO\IO;
use Windwalker\DI\Container;
use Windwalker\Ioc;

/**
 * Test class of \Windwalker\Ioc
 *
 * @since 2.1
 */
class IocTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test getApplication().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getApplication
	 */
	public function testGetApplication()
	{
		$this->assertSame(Container::getInstance()->get('app'), Ioc::getApplication());
	}

	/**
	 * Method to test getConfig().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getConfig
	 */
	public function testGetConfig()
	{
		$this->assertSame(Container::getInstance()->get('joomla.config'), Ioc::getConfig());
	}

	/**
	 * Method to test getInput().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getInput
	 */
	public function testGetInput()
	{
		$this->assertSame(Container::getInstance()->get('input'), Ioc::getInput());
	}

	/**
	 * Method to test getLanguage().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getLanguage
	 */
	public function testGetLanguage()
	{
		$this->assertSame(Container::getInstance()->get('language'), Ioc::getLanguage());
	}

	/**
	 * Method to test getDocument().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getDocument
	 */
	public function testGetDocument()
	{
		$this->assertSame(Container::getInstance()->get('document'), Ioc::getDocument());
	}

	/**
	 * Method to test getDbo().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getDbo
	 */
	public function testGetDbo()
	{
		$this->assertSame(Container::getInstance()->get('db'), Ioc::getDbo());
	}

	/**
	 * Method to test getSession().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getSession
	 */
	public function testGetSession()
	{
		$this->assertSame(Container::getInstance()->get('session'), Ioc::getSession());
	}

	/**
	 * Method to test getDispatcher().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getDispatcher
	 */
	public function testGetDispatcher()
	{
		$this->assertSame(Container::getInstance()->get('event.dispatcher'), Ioc::getDispatcher());
	}

	/**
	 * Method to test getMailer().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getMailer
	 */
	public function testGetMailer()
	{
		$this->assertEquals(Container::getInstance()->get('mailer'), Ioc::getMailer());
	}

	/**
	 * Method to test getAsset().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getAsset
	 */
	public function testGetAsset()
	{
		$this->assertSame(Container::getInstance()->get('helper.asset'), Ioc::getAsset());
	}

	/**
	 * Method to test getIO().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::getIO
	 */
	public function testGetIO()
	{
		Container::getInstance()->set('io', new IO);

		$this->assertSame(Container::getInstance()->get('io'), Ioc::getIO());
	}

	/**
	 * Method to test get().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::get
	 */
	public function testGet()
	{
		$this->assertSame(Container::getInstance()->get('app'), Ioc::get('app'));
	}

	/**
	 * Method to test factory().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Ioc::factory
	 */
	public function testFactory()
	{
		$this->assertSame(Container::getInstance(), Ioc::factory());
	}
}