<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Helper\QueryHelper;

use Windwalker\Sqlsync\Helper\AbstractQueryHelper;

/**
 * Class MysqliQueryHelper
 */
class MysqliQueryHelper extends AbstractQueryHelper
{
	/**
	 * showCreateTable
	 *
	 * @param $table
	 *
	 * @return string
	 */
	public function showCreateTable($table)
	{
		return 'SHOW CREATE TABLE ' . $this->db->quoteName($this->db->escape($table));
	}

	/**
	 * showColumns
	 *
	 * @param $table
	 *
	 * @return string
	 */
	public function showColumns($table)
	{
		return 'SHOW FULL COLUMNS FROM ' . $this->db->quoteName($this->db->escape($table));
	}

	/**
	 * getAllData
	 *
	 * @param $table
	 *
	 * @return \JDatabaseQuery
	 */
	public function getAllData($table)
	{
		$query = $this->db->getQuery(true);

		return $query->select('*')->from($query->quoteName($table));
	}

	/**
	 * dropTable
	 *
	 * @param $table
	 *
	 * @return string
	 */
	public function dropTable($table)
	{
		return "DROP TABLE IF EXISTS `{$table}`";
	}
}