<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Joomla\Database;

use JDatabaseDriver as AbstractDatabaseDriver;
use JDatabaseQuery as Query;
use JDatabaseQueryElement as QueryElement;
use Windwalker\Compare\Compare;
use Windwalker\DI\Container;
use Windwalker\Helper\DatabaseHelper;

/**
 * Class QueryHelper
 */
class QueryHelper
{
	/**
	 * Property db.
	 *
	 * @var  AbstractDatabaseDriver
	 */
	protected $db = null;

	/**
	 * Property tables.
	 *
	 * @var  array
	 */
	protected $tables = array();

	/**
	 * Constructor.
	 *
	 * @param  AbstractDatabaseDriver  $db  The DB adapter.
	 */
	public function __construct(AbstractDatabaseDriver $db = null)
	{
		$this->db = $db ? : $this->getDb();
	}

	/**
	 * Add table alias and name to join list.
	 *
	 * @param   string   $alias      The table alias.
	 * @param   string   $table      The table name.
	 * @param   mixed    $condition  Join ON conditions, can be an array or string.
	 * @param   string   $joinType   The join type, default is LEFT.
	 * @param   boolean  $prefix     Add table alias as prefix before select field name.
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function addTable($alias, $table, $condition = null, $joinType = 'LEFT', $prefix = null)
	{
		$tableStorage = array();

		$tableStorage['name'] = $table;
		$tableStorage['join']  = strtoupper($joinType);

		if (is_array($condition))
		{
			$condition = array($condition);
		}

		if ($condition)
		{
			$condition = (string) new QueryElement('ON', $condition, ' AND ');
		}
		else
		{
			$tableStorage['join'] = 'FROM';
		}

		// Remove too many spaces
		$condition = preg_replace('/\s(?=\s)/', '', $condition);

		$tableStorage['condition'] = trim($condition);
		$tableStorage['prefix'] = $prefix;

		$this->tables[$alias] = $tableStorage;

		return $this;
	}

	/**
	 * Remove aa table from join list.
	 *
	 * @param   string  $alias  The table alias.
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function removeTable($alias)
	{
		if (!empty($this->tables[$alias]))
		{
			unset($this->tables[$alias]);
		}

		return $this;
	}

	/**
	 * Generate all selected fields from joined tables.
	 *
	 * This method will auto get all columns from every table and set the selected fields to: `alias`.`field` AS `alias_field`
	 *
	 * @return  array  Generated select fields.
	 */
	public function getSelectFields()
	{
		$fields = array();

		$i = 0;

		foreach ($this->tables as $alias => $table)
		{
			$columns = DatabaseHelper::getColumns($table['name']);

			foreach ($columns as $column => $type)
			{
				$prefix = $table['prefix'];

				if ($i === 0)
				{
					$prefix = $prefix === null ? false : true;
				}
				else
				{
					$prefix = $prefix === null ? true : false;
				}

				if ($prefix === true)
				{
					$fields[] = $this->db->quoteName("{$alias}.{$column}", "{$alias}_{$column}");
				}
				else
				{
					$fields[] = $this->db->quoteName("{$alias}.{$column}", "{$column}");
				}
			}

			$i++;
		}

		return $fields;
	}

	/**
	 * Register join tables to Query object.
	 *
	 * @param   Query  $query  The query object.
	 *
	 * @return  Query  Return the query object.
	 */
	public function registerQueryTables(Query $query)
	{
		foreach ($this->tables as $alias => $table)
		{
			if ($table['join'] == 'FROM')
			{
				$query->from($query->quoteName($table['name']) . ' AS ' . $query->quoteName($alias));
			}
			else
			{
				$query->join(
					$table['join'],
					$query->quoteName($table['name']) . ' AS ' . $query->quoteName($alias) . ' ' . $table['condition']
				);
			}
		}

		return $query;
	}

	/**
	 * Build conditions into query object.
	 *
	 * @param   Query  $query       The query object to add where conditions.
	 * @param   array  $conditions  The where conditions array to add to query object.
	 *
	 * @return  Query  Return the query object.
	 */
	public static function buildWheres(Query $query, array $conditions)
	{
		foreach ($conditions as $key => $value)
		{
			// NULL
			if ($value === null)
			{
				$query->where($query->format('%n = NULL', $key));
			}

			// If using Compare class, we convert it to string.
			elseif ($value instanceof Compare)
			{
				$query->where((string) static::buildCompare($key, $value, $query));
			}

			// If key is numeric, just send value to query where.
			elseif (is_numeric($key))
			{
				$query->where($value);
			}

			// If is array or object, we use "IN" condition.
			elseif (is_array($value) || is_object($value))
			{
				$value = array_map(array($query, 'quote'), (array) $value);

				$query->where($query->quoteName($key) . new QueryElement('IN ()', $value, ','));
			}

			// Otherwise, we use equal condition.
			else
			{
				$query->where($query->format('%n = %q', $key, $value));
			}
		}

		return $query;
	}

	/**
	 * buildCompare
	 *
	 * @param string|int  $key
	 * @param Compare     $value
	 * @param Query       $query
	 *
	 * @return  string
	 */
	public static function buildCompare($key, Compare $value, $query = null)
	{
		/** @var Query $query */
		$query = $query ? : Container::getInstance()->get('db')->getQuery(true);

		if (!is_numeric($key))
		{
			$value->setCompare1($key);
		}

		$value->setHandler(
			function($compare1, $compare2, $operator) use ($query)
			{
				return $query->format('%n ' . $operator . ' %q', $compare1, $compare2);
			}
		);

		return (string) $value;
	}

	/**
	 * getDb
	 *
	 * @return  AbstractDatabaseDriver
	 */
	public function getDb()
	{
		if (!$this->db)
		{
			$this->db = Container::getInstance()->get('db');
		}

		return $this->db;
	}

	/**
	 * setDb
	 *
	 * @param   AbstractDatabaseDriver $db
	 *
	 * @return  QueryHelper  Return self to support chaining.
	 */
	public function setDb($db)
	{
		$this->db = $db;

		return $this;
	}

	/**
	 * Method to get property Tables
	 *
	 * @return  array
	 */
	public function getTables()
	{
		return $this->tables;
	}

	/**
	 * Method to set property tables
	 *
	 * @param   array $tables
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setTables($tables)
	{
		$this->tables = $tables;

		return $this;
	}
}
