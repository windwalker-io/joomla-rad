<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation;

/**
 * The Action of Foreign Key operations.
 * 
 * @since  {DEPLOY_VERSION}
 */
class Action
{
	const CASCADE   = 'CASCADE';
	const NO_ACTION = 'NO ACTION';
	const RESTRICT  = 'NO ACTION';
	const SET_NULL  = 'SET NULL';
}
