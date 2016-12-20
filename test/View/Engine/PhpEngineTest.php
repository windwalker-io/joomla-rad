<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\View\Engine;

use Windwalker\Test\TestCase\AbstractBaseTestCase;
use Windwalker\View\Engine\PhpEngine;

/**
 * Test class of \Windwalker\View\Engine\PhpEngine
 *
 * @since 2.1
 */
class PhpEngineTest extends AbstractBaseTestCase
{
	/**
	 * Method to test execute().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\View\Engine\PhpEngine::execute
	 */
	public function testExecute()
	{
		$engine = $this->getEngine();

		$result = $engine->render('default', array('foo' => 'foobar'));

		$this->assertStringSafeEquals('<p>default</p>
foobar', $result);

		$result = $engine->render('php_engine', array('foo' => 'foobar'));

		$this->assertStringSafeEquals('PhpEngine Test
foobar
Temelate Foo', $result);
	}

	/**
	 * getEngine
	 *
	 * @return  PhpEngine
	 */
	protected function getEngine()
	{
		$engine = new PhpEngine;
		$paths = new \SplPriorityQueue;

		$paths->insert(__DIR__ . '/tmpl', 0);

		$engine->setLayout('default');
		$engine->setPaths($paths);

		return $engine;
	}
}
