<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Bundle\Stub;

use Windwalker\Bundle\AbstractBundle;

/**
 * Class StubBundle
 */
class StubBundle extends AbstractBundle
{
	/**
	 * Constructor
	 *
	 * @param string $name Bundle name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}
}
