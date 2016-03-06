<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation;

/**
 * The Action of Foreign Key operations.
 * 
 * @since  2.1
 */
class Action
{
	const CASCADE   = 'CASCADE';
	const NO_ACTION = 'NO ACTION';
	const RESTRICT  = 'NO ACTION';
	const SET_NULL  = 'SET NULL';
}
