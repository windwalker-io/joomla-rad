<?php
/**
 * Part of joomla341c project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Facade;

/**
 * The FacadeInterface class.
 * 
 * @since  {DEPLOY_VERSION}
 */
interface FacadeInterface
{
	/**
	 * The DI key to get data from container.
	 *
	 * @return  string
	 */
	public static function getDIKey();
}
