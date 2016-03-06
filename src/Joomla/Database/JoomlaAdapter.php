<?php
/**
 * Part of joomla336 project. 
 *
 * @copyright  Copyright (C) 2014 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Joomla\Database;

use Windwalker\Data\DataSet;
use Windwalker\DataMapper\Adapter\AbstractDatabaseAdapter;

/**
 * The DatabaseAdapter class.
 * 
 * @since  2.1
 */
class JoomlaAdapter extends AbstractDatabaseAdapter
{
	/**
	 * Property db.
	 *
	 * @var  \JDatabaseDriver
	 */
	protected $db;

	/**
	 * Constructor.
	 *
	 * @param \JDatabaseDriver $db          Database adapter.
	 * @param QueryHelper      $queryHelper Query helper object.
	 */
	public function __construct(\JDatabaseDriver $db = null, QueryHelper $queryHelper = null)
	{
		$this->db = $db ? : \JFactory::getDbo();

		$this->queryHelper = $queryHelper ? : new QueryHelper($this->db);
	}

	/**
	 * Do find action.
	 *
	 * @param string  $table      The table name.
	 * @param string  $select     The select fields, default is '*'.
	 * @param array   $conditions Where conditions, you can use array or Compare object.
	 * @param array   $orders     Order sort, can ba string, array or object.
	 * @param integer $start      Limit start number.
	 * @param integer $limit      Limit rows.
	 *
	 * @return  mixed Found rows data set.
	 */
	public function find($table, $select = '*', array $conditions = array(), array $orders = array(), $start = 0, $limit = null, $options = array())
	{
		$query = $this->db->getQuery(true);

		// Conditions.
		QueryHelper::buildWheres($query, $conditions);

		// Loop ordering
		foreach ($orders as $order)
		{
			$query->order($order);
		}

		// Select single table or joins.
		if ($table instanceof DataSet)
		{
			$queryHelper = clone $this->queryHelper;

			foreach ($table as $tableData)
			{
				$queryHelper->addTable($tableData->alias, $tableData->table, $tableData->conditions, $tableData->joinType);
			}

			$query = $queryHelper->registerQueryTables($query);

			$select = $select ? : $queryHelper->getSelectFields();
		}
		else
		{
			$query->from($table);

			$select = $select ? : '*';
		}

		// Build query
		$query->select($select);

		if (isset($options['group']))
		{
			$query->group($options['group']);
		}

		if (isset($options['having']))
		{
			$query->having($options['having']);
		}

		return $this->db->setQuery($query, $start, $limit)->loadObjectList();
	}

	/**
	 * Do create action.
	 *
	 * @param string $table The table name.
	 * @param mixed  $data  The data set contains data we want to store.
	 * @param string $pk    The primary key column name.
	 *
	 * @return  mixed  Data set data with inserted id.
	 */
	public function create($table, $data, $pk = null)
	{
		return $this->db->insertObject($table, $data, $pk);
	}

	/**
	 * Do update action.
	 *
	 * @param string $table         The table name.
	 * @param mixed  $data          Data set contain data we want to update.
	 * @param array  $condFields    The where condition tell us record exists or not, if not set,
	 *                              will use primary key instead.
	 * @param bool   $updateNulls   Update empty fields or not.
	 *
	 * @throws \Exception
	 * @return  mixed Updated data set.
	 */
	public function updateOne($table, $data, array $condFields = array(), $updateNulls = false)
	{
		return $this->db->updateObject($table, $data, $condFields, $updateNulls);
	}

	/**
	 * Do updateAll action.
	 *
	 * @param string $table      The table name.
	 * @param mixed  $data       The data we want to update to every rows.
	 * @param mixed  $conditions Where conditions, you can use array or Compare object.
	 *
	 * @throws \Exception
	 * @return  boolean
	 */
	public function updateAll($table, $data, array $conditions = array())
	{
		$query = $this->db->getQuery(true);

		foreach ($data as $key => $value)
		{
			$query->set($query->format('%n = %q', $key, $value));
		}

		// Conditions.
		QueryHelper::buildWheres($query, $conditions);

		$query->update($table);

		return $this->db->setQuery($query)->execute();
	}

	/**
	 * Do delete action, this method should be override by sub class.
	 *
	 * @param string $table      The table name.
	 * @param mixed  $conditions Where conditions, you can use array or Compare object.
	 *
	 * @throws \Exception
	 * @return  boolean Will be always true.
	 */
	public function delete($table, array $conditions = array())
	{
		$query = $this->db->getQuery(true);

		// Conditions.
		QueryHelper::buildWheres($query, $conditions);

		$query->delete($table);

		return $this->db->setQuery($query)->execute();
	}

	/**
	 * Get table fields.
	 *
	 * @param string $table Table name.
	 *
	 * @return  array
	 */
	public function getFields($table)
	{
		$columns = $this->db->getTableColumns($table);

		return array_keys($columns);
	}

	/**
	 * transactionStart
	 *
	 * @param bool $asSavePoint
	 *
	 * @return  $this
	 */
	public function transactionStart($asSavePoint = false)
	{
		$this->db->transactionStart($asSavePoint);
	}

	/**
	 * transactionCommit
	 *
	 * @param bool $asSavePoint
	 *
	 * @return  $this
	 */
	public function transactionCommit($asSavePoint = false)
	{
		$this->db->transactionCommit($asSavePoint);
	}

	/**
	 * transactionRollback
	 *
	 * @param bool $asSavePoint
	 *
	 * @return  $this
	 */
	public function transactionRollback($asSavePoint = false)
	{
		$this->db->transactionRollback($asSavePoint);
	}

	/**
	 * Method to get property Db
	 *
	 * @return  \JDatabaseDriver
	 */
	public function getDb()
	{
		return $this->db;
	}

	/**
	 * Method to set property db
	 *
	 * @param   \JDatabaseDriver $db
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setDb($db)
	{
		$this->db = $db;

		return $this;
	}
}
