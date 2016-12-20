<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\View\Engine;

use Windwalker\DI\Container;
use Windwalker\Registry\Registry;
use Windwalker\Test\TestCase\AbstractBaseTestCase;
use Windwalker\Test\TestHelper;
use Windwalker\Test\View\Engine\Stub\StubEngine;

/**
 * Test class of \Windwalker\View\Engine\AbstractEngine
 *
 * @since 2.1
 */
class AbstractEngineTest extends AbstractBaseTestCase
{
	/**
	 * Method to test __construct().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::__construct
	 */
	public function test__construct()
	{
		$engine = new StubEngine;

		$this->assertEquals(new Registry(array()), TestHelper::getValue($engine, 'config'));
		$this->assertSame(Container::getInstance(), TestHelper::getValue($engine, 'container'));
		$this->assertEquals(new \SplPriorityQueue, TestHelper::getValue($engine, 'paths'));

		$paths = new \SplPriorityQueue;
		$container = new Container;

		$paths->insert(__DIR__ . '/tmpl', 0);

		$engine = new StubEngine(array('foo' => 'bar'), $container, $paths);

		$this->assertEquals(new Registry(array('foo' => 'bar')), TestHelper::getValue($engine, 'config'));
		$this->assertSame($container, TestHelper::getValue($engine, 'container'));
		$this->assertEquals($paths, TestHelper::getValue($engine, 'paths'));
	}

	/**
	 * Method to test render().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::render
	 */
	public function testRender()
	{
		$engine = new StubEngine;
		$paths = new \SplPriorityQueue;

		$paths->insert(__DIR__ . '/tmpl', 0);

		$engine->setPaths($paths);

		$result = $engine->render('default', array('foo' => 'bar'));
		$expected = realpath(__DIR__ . '/tmpl/default.php') . '{"foo":"bar"}';

		$this->assertEquals($expected, $result);
	}

	/**
	 * Method to test loadTemplate().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::loadTemplate
	 */
	public function testLoadTemplate()
	{
		$engine = new StubEngine;
		$paths = new \SplPriorityQueue;

		$paths->insert(__DIR__ . '/tmpl', 0);

		$engine->setLayout('default');
		$engine->setPaths($paths);

		$result = $engine->loadTemplate('foo', array('bar' => 'foo'));
		$expected = realpath(__DIR__ . '/tmpl/default_foo.php') . '{"bar":"foo"}';

		$this->assertEquals($expected, $result);
	}

	/**
	 * Method to test loadTemplate().
	 *
	 * @return void
	 *
	 * @expectedException \Exception
	 * @expectedExceptionMessage JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::loadTemplate
	 */
	public function testLoadTemplateException()
	{
		$engine = new StubEngine;

		$engine->loadTemplate();
	}

	/**
	 * Method to test escape().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::escape
	 */
	public function testEscape()
	{
		$engine = new StubEngine;

		$this->assertEquals('#&amp;&lt;&gt;&quot;\'', $engine->escape('#&<>"\''));
	}

	/**
	 * Method to test getLayoutTemplate().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::getLayoutTemplate
	 */
	public function testGetLayoutTemplate()
	{
		$engine = new StubEngine;
		$layoutTemplate = 'template';

		TestHelper::setValue($engine, 'layoutTemplate', $layoutTemplate);

		$this->assertEquals($layoutTemplate, $engine->getLayoutTemplate());
	}

	/**
	 * Method to test getPath().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::getPath
	 */
	public function testGetPath()
	{
		$engine = new StubEngine;
		$paths = new \SplPriorityQueue;

		$paths->insert(__DIR__ . '/tmpl', 0);

		$engine->setLayout('default');
		$engine->setPaths($paths);

		$result = $engine->loadTemplate('foo');

		$this->assertStringSafeEquals(realpath(__DIR__ . '/tmpl/default_foo.php'), $result);
	}

	/**
	 * Method to test getPaths().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::getPaths
	 */
	public function testGetPaths()
	{
		$engine = new StubEngine;

		$this->assertSame(TestHelper::getValue($engine, 'paths'), $engine->getPaths());
	}

	/**
	 * Method to test setPaths().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::setPaths
	 */
	public function testSetPaths()
	{
		$engine = new StubEngine;
		$paths = new \SplPriorityQueue;

		$paths->insert(__DIR__ . '/tmpl', 0);

		$engine->setPaths($paths);

		$this->assertSame($paths, TestHelper::getValue($engine, 'paths'));
	}

	/**
	 * Method to test getLayout().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::getLayout
	 */
	public function testGetLayout()
	{
		$engine = new StubEngine;

		$this->assertEquals(TestHelper::getValue($engine, 'layout'), $engine->getLayout());
	}

	/**
	 * Method to test setLayout().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::setLayout
	 */
	public function testSetLayout()
	{
		$engine = new StubEngine;

		$engine->setLayout('default');
		$this->assertEquals('default', TestHelper::getValue($engine, 'layout'));

		$engine->setLayout('template:default');
		$this->assertEquals('default', TestHelper::getValue($engine, 'layout'));
		$this->assertEquals('template', TestHelper::getValue($engine, 'layoutTemplate'));
	}

	/**
	 * Method to test getContainer().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::getContainer
	 */
	public function testGetContainer()
	{
		$engine = new StubEngine;

		$this->assertSame(Container::getInstance(), $engine->getContainer());

		$container = new Container;

		$engine = new StubEngine(array(), $container);

		$this->assertSame($container, $engine->getContainer());
	}

	/**
	 * Method to test setContainer().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\AbstractEngine::setContainer
	 */
	public function testSetContainer()
	{
		$engine = new StubEngine;
		$container = new Container;

		$engine->setContainer($container);

		$this->assertSame($container, TestHelper::getValue($engine, 'container'));
	}
}
