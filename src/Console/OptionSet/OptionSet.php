<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Console\OptionSet;

/**
 * Class OptionSet
 *
 * @since 2.0
 */
class OptionSet extends \ArrayObject
{
	/**
	 * Instance object.
	 *
	 * @var OptionSet
	 */
	protected static $instance;

	/**
	 * Get object Instance.
	 *
	 * @return  OptionSet
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new static;
		}

		return self::$instance;
	}
}
