<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Table;

use JTable;
use Windwalker\DI\Container;

/**
 * Windwalker active record Table.
 *
 * @since 2.0
 */
class Table extends \JTable
{
	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   string           $table  Name of the table to model.
	 * @param   mixed            $key    Name of the primary key field in the table or array of field names that compose the primary key.
	 * @param   \JDatabaseDriver $db     JDatabaseDriver object.
	 */
	public function __construct($table, $key = 'id', $db = null)
	{
		$db = $db ?: Container::getInstance()->get('db');

		parent::__construct($table, $key, $db);
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		if (property_exists($this, 'params') && !empty($this->params))
		{
			if (is_array($this->params) || is_object($this->params))
			{
				$this->params = json_encode($this->params);
			}
		}

		return parent::store($updateNulls);
	}
}
