<?php
/**
 * Part of joomla34b project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Helper;

use Windwalker\Facade\AbstractFacade;

/**
 * The DatabaseHelper class.
 *
 * @method  static  \JDatabaseDriver  getInstance()
 * 
 * @since  {DEPLOY_VERSION}
 */
class DatabaseHelper extends AbstractFacade
{
	/**
	 * The DI key to get data from container.
	 *
	 * @return  string
	 */
	public static function getDIKey()
	{
		return 'db';
	}

	/**
	 * Execute a query.
	 *
	 * @param string|\JDatabaseQuery $query
	 *
	 * @return  mixed
	 */
	public static function query($query)
	{
		return static::getInstance()->setQuery($query)->execute();
	}

	/**
	 * Batch execute queries.
	 *
	 * @param string $queries
	 *
	 * @return  boolean[]
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
	 * loadAll
	 *
	 * @param string|\JDatabaseQuery $query
	 *
	 * @return  \JDatabaseDriver
	 */
	public static function setQuery($query)
	{
		$db = static::getInstance();

		return $db->setQuery($query);
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
