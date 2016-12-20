<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Provider;

use Windwalker\DI\Container;
use Windwalker\Provider\WebProvider;

/**
 * Test class of \Windwalker\Provider\WebProvider
 *
 * @since 2.1
 */
class WebProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Method to test register().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Provider\WebProvider::register
	 */
	public function testRegister()
	{
		$container = new Container;
		$provider = new WebProvider;

		$provider->register($container);

		$this->assertSame(\JFactory::getApplication(), $container->get('app'));
		$this->assertSame(\JFactory::getApplication(), $container->get('JApplicationCms'));
		$this->assertSame(\JFactory::getDocument(), $container->get('document'));
		$this->assertSame(\JFactory::getDocument(), $container->get('JDocumentHtml'));
		$this->assertInstanceOf('JUser', $container->get('user'));
		$this->assertInstanceOf('JUser', $container->get('JUser'));
		$this->assertSame(\JFactory::getApplication()->input, $container->get('input'));
		$this->assertSame(\JFactory::getApplication()->input, $container->get('JInput'));
	}
}
