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
	/**
	 * Delete or update the row from the parent table, and automatically delete or update the matching rows
	 * in the child table.
	 *
	 * @const  string
	 */
	const CASCADE   = 'CASCADE';

	/**
	 * Rejects the delete or update operation for the parent table.
	 *
	 * @const  string
	 */
	const NO_ACTION = 'NO ACTION';

	/**
	 * Rejects the delete or update operation for the parent table.
	 *
	 * Same as NO_ACTION.
	 *
	 * @const  string
	 */
	const RESTRICT  = 'NO ACTION';

	/**
	 * Delete or update the row from the parent table, and set the foreign key column or columns in the child table to NULL.
	 *
	 * @const  string
	 */
	const SET_NULL  = 'SET NULL';
}
