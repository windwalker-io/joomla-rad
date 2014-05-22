<?php
/**
 * Part of joomla330 project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Table;

/**
 * Class TableHelper
 *
 * @since 1.0
 */
class TableHelper
{
	/**
	 * Database adapter.
	 *
	 * @var  \JDatabaseDriver
	 */
	protected $db = null;

	/**
	 * The table name.
	 *
	 * @var  string
	 */
	protected $table = null;

	/**
	 * The primary key name.
	 *
	 * @var  string
	 */
	protected $pkName = null;

	/**
	 * Class init.
	 *
	 * @param string           $table  The table name.
	 * @param \JDatabaseDriver $db     Database adapter.
	 * @param string           $pkName The primary key name.
	 */
	public function __construct($table, \JDatabaseDriver $db, $pkName = 'id')
	{
		$this->db = $db ? : $this->getDb();
		$this->table = $table;
		$this->pkName = $pkName;
	}

	/**
	 * Initialise a new record and return id.
	 *
	 * @param int|string  $id   The id to find.
	 * @param mixed       $row  The added row.
	 *
	 * @throws \InvalidArgumentException
	 * @return  bool|string|int Return init id.
	 */
	public function initRow($id, $row = array())
	{
		if ($this->exists($id))
		{
			return $id;
		}

		$row = $row ? (object) $row : new \stdClass;

		$row->{$this->pkName} = $id;

		if (! $this->db->insertObject($this->table, $row, $this->pkName))
		{
			return false;
		}

		return $row->{$this->pkName};
	}

	/**
	 * Is an id exists?
	 *
	 * @param int|string $id The id to find.
	 *
	 * @return  boolean True if exists.
	 */
	public function exists($id)
	{
		$query = $this->db->getQuery(true);

		$query->select($this->pkName)
			->from($this->table)
			->where($query->format('%n = %q', $this->pkName, $id));

		if ($this->db->setQuery($query)->loadResult())
		{
			return true;
		}

		return false;
	}

	/**
	 * Get DB adapter.
	 *
	 * @return  \JDatabaseDriver The DB adapter.
	 */
	public function getDb()
	{
		if (!$this->db)
		{
			$this->db = \JFactory::getDbo();
		}

		return $this->db;
	}

	/**
	 * Set DB adapter.
	 *
	 * @param   \JDatabaseDriver $db
	 *
	 * @return  TableHelper  Return self to support chaining.
	 */
	public function setDb($db)
	{
		$this->db = $db;

		return $this;
	}
}
