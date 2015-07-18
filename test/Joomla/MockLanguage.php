<?php
/**
 * Part of windwalker-rad-dev project. 
 *
 * @copyright  Copyright (C) 2011 - 2015 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Joomla;

class MockLanguage extends \JLanguage
{
	/**
	 * Property loadExecuted.
	 *
	 * @var  bool
	 */
	public $loadExecuted = false;

	/**
	 * Mock load method
	 *
	 * @param string $extension
	 * @param string $basePath
	 * @param null   $lang
	 * @param bool   $reload
	 * @param bool   $default
	 *
	 * @return  string
	 */
	public function load($extension = 'joomla', $basePath = JPATH_BASE, $lang = null, $reload = false, $default = true)
	{
		$this->loadExecuted = true;

		return 'Load did executed!';
	}
}
