<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Object;

/**
 * The Null Object Interface
 */
interface NullObjectInterface
{
	/**
	 * Is this object not contain any values.
	 *
	 * @return boolean
	 */
	public function isNull();
}
