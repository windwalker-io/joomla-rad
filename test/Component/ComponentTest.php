<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Component;

use Windwalker\Component\Component;
use Windwalker\Test\TestHelper;

/**
 * Test class of Windwalker\Component\Component
 *
 * @since 2.0
 */
class ComponentTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $componentName = 'testcomponent';

	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$dirs = array(
			JPATH_ROOT . '/components/com_testcomponent',
			JPATH_ROOT . '/administrator/components/com_testcomponent/src/Testcomponent/Listener',
		);

		// Add component folders
		foreach ($dirs as $dir)
		{
			if (!is_dir($dir))
			{
				mkdir($dir, 0777, true);
			}
		}
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
		// Remove component files
		$this->removeDirectory(JPATH_BASE . '/components/com_testcomponent');
		$this->removeDirectory(JPATH_BASE . '/administrator/components/com_testcomponent');
	}

	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Component\Component::__construct
	 * @covers \Windwalker\Component\Component::init
	 */
	public function testConstructor()
	{
		$component = new Component($this->componentName);

		$this->assertInstanceOf('Windwalker\Test\Application\TestApplication', $component->getApplication());
		$this->assertInstanceOf('Windwalker\DI\Container', $component->getContainer());
		$this->assertInstanceOf('JInput', $component->getInput());
		$this->assertNull($component->getDefaultController());

		$name   = TestHelper::getValue($component, 'name');
		$option = TestHelper::getValue($component, 'option');
		$path   = TestHelper::getValue($component, 'path');

		$this->assertEquals($this->componentName, $name);
		$this->assertEquals('com_' . $this->componentName, $option);
		$this->assertEquals(array(
			'self'          => JPATH_ROOT . '/components/com_' . strtolower($this->componentName),
			'site'          => JPATH_ROOT . '/components/com_' . strtolower($this->componentName),
			'administrator' => JPATH_ROOT . '/administrator/components/com_' . strtolower($this->componentName),
		), $path);
	}

	/**
	 * testConstructorException
	 *
	 * @return  void
	 *
	 * @expectedException \Exception
	 * @expectedExceptionMessage Component need name.
	 *
	 * @covers \Windwalker\Component\Component::__construct
	 */
	public function testConstructorException()
	{
		new Component;
	}

	/**
	 * Method to test registerTask().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Component\Component::registerTask
	 * @TODO   Implement testRegisterTask().
	 */
	public function testRegisterTask()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test unregisterTask().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Component\Component::unregisterTask
	 * @TODO   Implement testUnregisterTask().
	 */
	public function testUnregisterTask()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test getActions().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Component\Component::getActions
	 * @TODO   Implement testGetActions().
	 */
	public function testGetActions()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * testExecute
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Component\Component::execute
	 */
	public function testExecute()
	{
		$controller = $this->getMock('Windwalker\Controller\Controller', array('doExecute', 'setComponentPath', 'setContainer', 'execute'));

		$controller->expects($this->once())
			->method('setComponentPath')
			->will($this->returnSelf());

		$controller->expects($this->once())
			->method('setContainer')
			->will($this->returnSelf());

		$controller->expects($this->once())
			->method('execute')
			->will($this->returnValue('foobar'));

		$controllerResolver = $this->getMockBuilder('Windwalker\Controller\Resolver\ControllerResolver')
			->disableOriginalConstructor()
			->getMock();

		$controllerResolver->expects($this->once())
			->method('getController')
			->will($this->returnValue($controller));

		$component = new Component($this->componentName);

		$container = $component->getContainer();

		// Backup origin controller resolver
		$backupControllerResolver = $container->get('controller.resolver');

		$container->set('\\Windwalker\\Controller\\Resolver\\ControllerResolver', $controllerResolver);

		$this->assertEquals('foobar', $component->execute());

		// Restore origin controller resolver
		$container->set('\\Windwalker\\Controller\\Resolver\\ControllerResolver', $backupControllerResolver);
	}

	/**
	 * testGetReflection
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Component\Component::getReflection
	 */
	public function testGetReflection()
	{
		$component = new Component($this->componentName);

		$this->assertEquals(new \ReflectionClass($component), $component->getReflection());
	}

	/**
	 * testGetPath
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Component\Component::getPath
	 */
	public function testGetPath()
	{
		$component = new Component($this->componentName);

		$site = JPATH_ROOT . '/components/com_' . strtolower($this->componentName);
		$admin = JPATH_ROOT . '/administrator/components/com_' . strtolower($this->componentName);

		$this->assertEquals($site, $component->getPath());
		$this->assertEquals($site, $component->getPath('site'));
		$this->assertEquals($admin, $component->getPath('admin'));
		$this->assertEquals($admin, $component->getPath('administrator'));
	}

	/**
	 * testGetSitePath
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Component\Component::getSitePath
	 */
	public function testGetSitePath()
	{
		$component = new Component($this->componentName);

		$site = JPATH_ROOT . '/components/com_' . strtolower($this->componentName);

		$this->assertEquals($site, $component->getSitePath());
	}

	/**
	 * testGetAdminPath
	 *
	 * @return  void
	 *
	 * @covers \Windwalker\Component\Component::getAdminPath
	 */
	public function testGetAdminPath()
	{
		$component = new Component($this->componentName);

		$admin = JPATH_ROOT . '/administrator/components/com_' . strtolower($this->componentName);

		$this->assertEquals($admin, $component->getAdminPath());
	}

	/**
	 * Delete directory recursively (will delete all files in the directory)
	 *
	 * @param string $dir
	 *
	 * @return bool
	 */
	protected function removeDirectory($dir)
	{
		$files = array_diff(scandir($dir), array('.', '..'));

		foreach ($files as $file)
		{
			(is_dir("$dir/$file")) ? $this->removeDirectory("$dir/$file") : unlink("$dir/$file");
		}

		return rmdir($dir);
	}
}
