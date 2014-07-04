<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Model\Helper;

use Windwalker\DI\Container;
use Windwalker\Helper\DateHelper;
use Windwalker\Joomla\Database\DatabaseFactory;

/**
 * The Query Helper
 *
 * @since 2.0
 */
class QueryHelper extends \Windwalker\Joomla\Database\QueryHelper
{
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
			$columns = DatabaseFactory::getCommand()->getColumns($table['name']);

			foreach ($columns as $key => $var)
			{
				$fields[] = "{$alias}.{$key}";
			}
		}

		return $fields;
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
	 * Simple highlight for SQL queries.
	 *
	 * @param   string  $query  The query to highlight.
	 *
	 * @return  string  Highlighted query string.
	 */
	public static function highlightQuery($query)
	{
		$newlineKeywords = '#\b(FROM|LEFT|INNER|OUTER|WHERE|SET|VALUES|ORDER|GROUP|HAVING|LIMIT|ON|AND|CASE)\b#i';

		$query = htmlspecialchars($query, ENT_QUOTES);

		$query = preg_replace($newlineKeywords, '<br />&#160;&#160;\\0', $query);

		$regex = array(

			// Tables are identified by the prefix.
			'/(=)/'
			=> '<strong class="text-error">$1</strong>',

			// All uppercase words have a special meaning.
			'/(?<!\w|>)([A-Z_]{2,})(?!\w)/x'
			=> '<span class="text-info">$1</span>',

			// Tables are identified by the prefix.
			'/(' . \JFactory::getDbo()->getPrefix() . '[a-z_0-9]+)/'
			=> '<span class="text-success">$1</span>'

		);

		$query = preg_replace(array_keys($regex), array_values($regex), $query);

		$query = str_replace('*', '<strong style="color: red;">*</strong>', $query);

		return $query;
	}
}
