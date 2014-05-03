<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model\Helper;

use JDatabaseDriver;
use JDatabaseQuery;
use Windwalker\DI\Container;
use Windwalker\Helper\DateHelper;

/**
 * The Query Helper
 *
 * @since 2.0
 */
class QueryHelper
{
	/**
	 * THe first table only select columns' name.
	 *
	 * For example: `item.title AS title`
	 *
	 * @const integer
	 */
	const COLS_WITH_FIRST = 1;

	/**
	 * The first table select column with prefix as alias.
	 *
	 * For example: `item.title AS item_title`
	 *
	 * @const integer
	 */
	const COLS_PREFIX_WITH_FIRST = 2;

	/**
	 * A cache to store Table columns.
	 *
	 * @var array
	 */
	protected $columnCache;

	/**
	 * THe db adapter.
	 *
	 * @var  JDatabaseDriver
	 */
	protected $db = null;

	/**
	 * Tables storage.
	 *
	 * @var  array
	 */
	protected $tables = array();

	/**
	 * Constructor.
	 *
	 * @param JDatabaseDriver $db The db adapter.
	 */
	public function __construct(JDatabaseDriver $db = null)
	{
		$this->db = $db ? : $this->getDb();
	}

	/**
	 * Add a table into storage.
	 *
	 * Example: `addTable('item', '#__items', 'item.catid = cat.id')`
	 *
	 * @param string $alias     Table select alias.
	 * @param string $table     Table name.
	 * @param mixed  $condition Join conditions, use string or array.
	 * @param string $joinType  The Join type.
	 *
	 * @return  QueryHelper  Return self to support chaining.
	 */
	public function addTable($alias, $table, $condition = null, $joinType = 'LEFT')
	{
		$tableStorage = array();

		$tableStorage['name'] = $table;
		$tableStorage['join']  = strtoupper($joinType);

		if ($condition)
		{
			$condition = (string) new \JDatabaseQueryElement('ON', (array) $condition, ' AND ');
		}
		else
		{
			$tableStorage['join'] = 'FROM';
		}

		// Remove too many spaces
		$condition = preg_replace('/\s(?=\s)/', '', $condition);

		$tableStorage['condition'] = trim($condition);

		$this->tables[$alias] = $tableStorage;

		return $this;
	}

	/**
	 * Remove a table from storage.
	 *
	 * @param string $alias Table alias.
	 *
	 * @return  QueryHelper Return self to support chaining.
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
	 * Get select fields.
	 *
	 * @param int $prefixFirst Prefix first.
	 *
	 * @return  array Select fields.
	 */
	public function getSelectFields($prefixFirst = self::COLS_WITH_FIRST)
	{
		$fields = array();

		$i = 0;

		foreach ($this->tables as $alias => $table)
		{
			if (empty($this->columnCache[$table['name']]))
			{
				$this->columnCache[$table['name']] = $this->db->getTableColumns($table['name']);
			}

			$columns = $this->columnCache[$table['name']];

			foreach ($columns as $column => $var)
			{
				if ($i === 0)
				{
					if ($prefixFirst & self::COLS_WITH_FIRST)
					{
						$fields[] = $this->db->quoteName("{$alias}.{$column}", $column);
					}

					if ($prefixFirst & self::COLS_PREFIX_WITH_FIRST)
					{
						$fields[] = $this->db->quoteName("{$alias}.{$column}", "{$alias}_{$column}");
					}
				}
				else
				{
					$fields[] = $this->db->quoteName("{$alias}.{$column}", "{$alias}_{$column}");
				}
			}

			$i++;
		}

		return $fields;
	}

	/**
	 * Filter fields.
	 *
	 * @return  array Filter fields.
	 */
	public function getFilterFields()
	{
		$fields = array();

		foreach ($this->tables as $alias => $table)
		{
			if (empty($this->columnCache[$table['name']]))
			{
				$this->columnCache[$table['name']] = $this->db->getTableColumns($table['name']);
			}

			$columns = $this->columnCache[$table['name']];

			foreach ($columns as $key => $var)
			{
				$fields[] = "{$alias}.{$key}";
			}
		}

		return $fields;
	}

	/**
	 * Register query table.
	 *
	 * @param JDatabaseQuery $query The db query.
	 *
	 * @return  JDatabaseQuery The db query object.
	 */
	public function registerQueryTables(JDatabaseQuery $query)
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
	 * Get a query string to filter the publishing items now.
	 *
	 * Will return: '( publish_up < 'xxxx-xx-xx' OR publish_up = '0000-00-00' )
	 *   AND ( publish_down > 'xxxx-xx-xx' OR publish_down = '0000-00-00' )'
	 *
	 * @param   string $prefix Prefix to columns name, eg: 'a.' will use `a`.`publish_up`.
	 *
	 * @return  string Query string.
	 */
	public static function publishingPeriod($prefix = '')
	{
		$db       = Container::getInstance()->get('db');
		$nowDate  = $date = DateHelper::getDate()->toSQL();
		$nullDate = $db->getNullDate();

		$date_where = " ( {$prefix}publish_up < '{$nowDate}' OR  {$prefix}publish_up = '{$nullDate}') AND " .
			" ( {$prefix}publish_down > '{$nowDate}' OR  {$prefix}publish_down = '{$nullDate}') ";

		return $date_where;
	}

	/**
	 * Get a query string to filter the publishing items now, and the published > 0.
	 *
	 * Will return: `( publish_up < 'xxxx-xx-xx' OR publish_up = '0000-00-00' )
	 *    AND ( publish_down > 'xxxx-xx-xx' OR publish_down = '0000-00-00' )
	 *    AND published >= '1' `
	 *
	 * @param   string $prefix        Prefix to columns name, eg: 'a.' will use `a.publish_up`.
	 * @param   string $published_col The published column name. Usually 'published' or 'state' for com_content.
	 *
	 * @return  string  Query string.
	 */
	public static function publishingItems($prefix = '', $published_col = 'published')
	{
		return self::publishingPeriod($prefix) . " AND {$prefix}{$published_col} >= '1' ";
	}

	/**
	 * Get db adapter.
	 *
	 * @return  \JDatabaseDriver Db adapter.
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
	 * Set db adapter.
	 *
	 * @param   \JDatabaseDriver $db The db adapter.
	 *
	 * @return  QueryHelper  Return self to support chaining.
	 */
	public function setDb($db)
	{
		$this->db = $db;

		return $this;
	}
}
