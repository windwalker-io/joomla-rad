<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Helper;

/**
 * Class AbstractQueryHelper
 */
abstract class AbstractQueryHelper
{
	/**
	 * @var array
	 */
	static protected $instance = array();

	/**
	 * getInstance
	 *
	 * @param string $type
	 *
	 * @return mixed
	 */
	static public function getInstance($type = 'mysql')
	{
		if (!empty(self::$instance[$type]))
		{
			return self::$instance[$type];
		}

		$class = 'Windwalker\Sqlsync\\Helper\\QueryHelper\\' . ucfirst($type) . 'QueryHelper';

		return self::$instance[$type] = new $class;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->db = \JFactory::getDbo();
	}

	/**
	 * showCreateTable
	 *
	 * @param $table
	 *
	 * @return mixed
	 */
	abstract public function showCreateTable($table);

	/**
	 * showColumns
	 *
	 * @param $table
	 *
	 * @return mixed
	 */
	abstract public function showColumns($table);

	/**
	 * getAllData
	 *
	 * @param $table
	 *
	 * @return mixed
	 */
	abstract public function getAllData($table);

	/**
	 * dropTable
	 *
	 * @param $table
	 *
	 * @return mixed
	 */
	abstract public function dropTable($table);
}