<?php
/**
 * Part of windwalker-rad-dev project. 
 *
 * @copyright  Copyright (C) 2011 - 2015 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Table\Table;

/**
 * Item Table class.
 *
 * @since 1.0
 */
class StubTableCrudModel extends Table
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('#__test_table');
	}

}
