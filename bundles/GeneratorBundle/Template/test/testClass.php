<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later. see LICENSE
 */

namespace {{ test.class.namespace }};

/**
 * Test class of \{{ origin.class.name }}
 *
 * @since 2.1
 */
class {{ test.class.shortname }} extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var \{{ origin.class.name }}
	 */
	protected $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->instance = new \{{ origin.class.name }};
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
	{{ test.methods }}}