<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Object;

/**
 * The basic Object class with isNull() method.
 *
 * @since 2.0
 */
class Object extends \JObject implements NullObjectInterface
{
	/**
	 * Is this object not contain any values.
	 *
	 * @return boolean
	 */
	public function isNull()
	{
		return (boolean) $this->getProperties();
	}
}
