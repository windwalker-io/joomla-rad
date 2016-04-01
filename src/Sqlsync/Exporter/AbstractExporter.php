<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Windwalker\Sqlsync\Exporter;

use Windwalker\Sqlsync\Helper\AbstractQueryHelper;

/**
 * Class AbstractExporter
 *
 * @since  3.2
 */
abstract class AbstractExporter extends \JModelDatabase
{
	/**
	 * @var array
	 */
	static protected $instance = array();

	/**
	 * @var \JDatabaseDriver
	 */
	protected $db;

	/**
	 * @var AbstractQueryHelper
	 */
	protected $queryHelper;

	/**
	 * @var int
	 */
	protected $tableCount = 0;

	/**
	 * @var int
	 */
	protected $rowCount = 0;

	/**
	 * getInstance
	 *
	 * @param   string  $type  Sql type.
	 *
	 * @return  mixed
	 */
	static public function getInstance($type = 'sql')
	{
		if (!empty(self::$instance[$type]))
		{
			return self::$instance[$type];
		}

		$class = 'Windwalker\Sqlsync\\Exporter\\' . ucfirst($type) . 'Exporter';

		return self::$instance[$type] = new $class;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->queryHelper = AbstractQueryHelper::getInstance($this->db->name);
	}

	/**
	 * export
	 *
	 * @param bool $ignoreTrack
	 * @param bool $prefixOnly
	 *
	 * @return mixed
	 */
	abstract public function export($ignoreTrack = false, $prefixOnly = false);

	/**
	 * getCreateTable
	 *
	 * @param $table
	 *
	 * @return mixed
	 */
	abstract protected function getCreateTable($table);

	/**
	 * getInserts
	 *
	 * @param $table
	 *
	 * @return mixed
	 */
	abstract protected function getInserts($table);
}