<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Bundle\Stub\Command\Foo;

use Windwalker\Console\Command\AbstractCommand;

/**
 * Class FooCommand
 */
class FooCommand extends AbstractCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct('foo');
	}
}
