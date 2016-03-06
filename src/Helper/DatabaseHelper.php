<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use Windwalker\Debugger\Debugger;
use Windwalker\Facade\AbstractFacade;
use Windwalker\Model\Helper\QueryHelper;

/**
 * The DatabaseHelper class.
 *
 * @method  static  \JDatabaseDriver  getInstance()
 * 
 * @since  2.1
 */
class DatabaseHelper extends AbstractFacade
{
	/**
	 * Property The DI key.
	 *
	 * @var  string
	 */
	protected static $_key = 'db';

	/**
	 * Property columnCache.
	 *
	 * @var  array
	 */
	protected static $columnCache = array();

	/**
	 * Execute a query.
	 *
	 * @param   string|\JDatabaseQuery  $query  Execute a query instantly.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 */
	public static function query($query)
	{
		return static::getInstance()->setQuery($query)->execute();
	}

	/**
	 * Batch execute queries.
	 *
	 * @param   string  $queries  Multiple queries to execute.
	 *
	 * @return  boolean[]  All results.
	 */
	public static function batchQuery($queries)
	{
		$db = static::getInstance();

		$queries = $db->splitSql($queries);

		$results = array();

		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query)
			{
				$results = $db->setQuery($query)->execute();
			}
		}

		return $results;
	}

	/**
	 * Set query to DatabaseDriver.
	 *
	 * @param   string|\JDatabaseQuery  $query  The query object or string.
	 *
	 * @return  \JDatabaseDriver  Return DatabaseDriver to do something.
	 */
	public static function setQuery($query)
	{
		$db = static::getInstance();

		return $db->setQuery($query);
	}

	/**
	 * Get table columns.
	 *
	 * @param string $table Table name.
	 *
	 * @return  array Table columns with type.
	 */
	public static function getColumns($table)
	{
		if (empty(static::$columnCache[$table]))
		{
			$db = static::getInstance();

			static::$columnCache[$table] = $db->getTableColumns($table);
		}

		return static::$columnCache[$table];
	}

	/**
	 * Batch update some data.
	 *
	 * @param string $table      Table name.
	 * @param string $data       Data you want to update.
	 * @param mixed  $conditions Where conditions, you can use array or Compare object.
	 *                           Example:
	 *                           - `array('id' => 5)` => id = 5
	 *                           - `new GteCompare('id', 20)` => 'id >= 20'
	 *                           - `new Compare('id', '%Flower%', 'LIKE')` => 'id LIKE "%Flower%"'
	 *
	 * @return  boolean True if update success.
	 */
	public static function updateBatch($table, $data, $conditions = array())
	{
		$db = static::getInstance();

		$query = $db->getQuery(true);

		// Build conditions
		$query = QueryHelper::buildWheres($query, $conditions);

		// Build update values.
		$fields = array_keys(static::getColumns($table));

		$hasField = false;

		foreach ((array) $data as $field => $value)
		{
			if (!in_array($field, $fields))
			{
				continue;
			}

			$query->set($query->format('%n = %q', $field, $value));

			$hasField = true;
		}

		if (!$hasField)
		{
			return false;
		}

		$query->update($table);

		return $db->setQuery($query)->execute();
	}

	/**
	 * Quotes and optionally escapes a string to database requirements for use in database queries.
	 *
	 * @param   mixed    $text    A string or an array of strings to quote.
	 * @param   boolean  $escape  True (default) to escape the string, false to leave it unchanged.
	 *
	 * @return  string  The quoted input string.
	 */
	public static function quote($text, $escape = true)
	{
		return static::getInstance()->quote($text, $escape);
	}

	/**
	 * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
	 * risks and reserved word conflicts.
	 *
	 * @param   mixed  $name  The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
	 *                        Each type supports dot-notation name.
	 * @param   mixed  $as    The AS query part associated to $name. It can be string or array, in latter case it has to be
	 *                        same length of $name; if is null there will not be any AS part for string or array element.
	 *
	 * @return  mixed  The quote wrapped name, same type of $name.
	 */
	public static function quoteName($name, $as = null)
	{
		return static::getInstance()->quoteName($name, $as);
	}
}
