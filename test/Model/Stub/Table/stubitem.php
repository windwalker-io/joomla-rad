<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

use Windwalker\Table\Table;

/**
 * The WindwalkerTableStubItem class.
 * 
 * @since  2.1
 */
class WindwalkerTableStubitem extends Table
{
	/**
	 * Class constructor
	 *
	 * return  void
	 */
	public function __construct()
	{
		parent::__construct('#__test_table');
	}
}
