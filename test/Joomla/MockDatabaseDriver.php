<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Test\Joomla;

use JDatabaseDriver;
use RuntimeException;

/**
 * The MockDatabaseDriver class.
 * 
 * @since  2.1
 */
class MockDatabaseDriver extends \JDatabaseDriver
{
	/**
	 * Property executed.
	 *
	 * @var  array
	 */
	public $executed = array();

	/**
	 * Property lastQuery.
	 *
	 * @var  string
	 */
	public $lastQuery;

	/**
	 * mark
	 *
	 * @param string $method
	 *
	 * @return  static
	 */
	public function mark($method)
	{
		$this->executed[$method] = true;

		return $this;
	}

	/**
	 * executed
	 *
	 * @param string $method
	 *
	 * @return  boolean
	 */
	public function executed($method)
	{
		return !empty($this->executed[$method]);
	}

	/**
	 * Class init.
	 */
	public function __construct()
	{
		parent::__construct(array());

		$this->name = \JFactory::getConfig()->get('driver', 'mysqli');
	}

	/**
	 * Test to see if the connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.2
	 */
	public static function isSupported()
	{
		return true;
	}

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function connect()
	{
		$this->mark(__FUNCTION__);
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 * @since   11.1
	 */
	public function connected()
	{
		$this->mark(__FUNCTION__);

		return true;
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function disconnect()
	{
		$this->mark(__FUNCTION__);
	}

	/**
	 * Drops a table from the database.
	 *
	 * @param   string  $table    The name of the database table to drop.
	 * @param   boolean $ifExists Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  JDatabaseDriver     Returns this object to support chaining.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function dropTable($table, $ifExists = true)
	{
		$this->mark(__FUNCTION__);

		return $this;
	}

	/**
	 * Escapes a string for usage in an SQL statement.
	 *
	 * @param   string  $text  The string to be escaped.
	 * @param   boolean $extra Optional parameter to provide extra escaping.
	 *
	 * @return  string   The escaped string.
	 *
	 * @since   11.1
	 */
	public function escape($text, $extra = false)
	{
		$this->mark(__FUNCTION__);

		return $text;
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchArray($cursor = null)
	{
		$this->mark(__FUNCTION__);

		return array();
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchAssoc($cursor = null)
	{
		$this->mark(__FUNCTION__);

		return array();
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed  $cursor The optional result set cursor from which to fetch the row.
	 * @param   string $class  The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		$this->mark(__FUNCTION__);

		return new \stdClass;
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function freeResult($cursor = null)
	{
		$this->mark(__FUNCTION__);
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows.
	 *
	 * @since   11.1
	 */
	public function getAffectedRows()
	{
		$this->mark(__FUNCTION__);

		return 123;
	}

	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database or boolean false if not supported.
	 *
	 * @since   11.1
	 */
	public function getCollation()
	{
		$this->mark(__FUNCTION__);

		return 'UTF-8';
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource $cursor An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   11.1
	 */
	public function getNumRows($cursor = null)
	{
		$this->mark(__FUNCTION__);

		return 321;
	}

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @param   string  $table    The name of the database table.
	 * @param   boolean $typeOnly True (default) to only return field types.
	 *
	 * @return  array  An array of fields by table.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$this->mark(__FUNCTION__);

		return array();
	}

	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * @param   mixed $tables A table name or a list of table names.
	 *
	 * @return  array  A list of the create SQL for the tables.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function getTableCreate($tables)
	{
		$this->mark(__FUNCTION__);

		return array();
	}

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @param   mixed $tables A table name or a list of table names.
	 *
	 * @return  array  An array of keys for the table(s).
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function getTableKeys($tables)
	{
		$this->mark(__FUNCTION__);

		return array();
	}

	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function getTableList()
	{
		$this->mark(__FUNCTION__);

		return array();
	}

	/**
	 * Get the version of the database connector
	 *
	 * @return  string  The database connector version.
	 *
	 * @since   11.1
	 */
	public function getVersion()
	{
		$this->mark(__FUNCTION__);

		return '1.0';
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  mixed  The value of the auto-increment field from the last inserted row.
	 *
	 * @since   11.1
	 */
	public function insertid()
	{
		$this->mark(__FUNCTION__);

		return 1221;
	}

	/**
	 * Locks a table in the database.
	 *
	 * @param   string $tableName The name of the table to unlock.
	 *
	 * @return  JDatabaseDriver     Returns this object to support chaining.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function lockTable($tableName)
	{
		$this->mark(__FUNCTION__);

		return $this;
	}

	/**
	 * Renames a table in the database.
	 *
	 * @param   string $oldTable The name of the table to be renamed
	 * @param   string $newTable The new name for the table.
	 * @param   string $backup   Table prefix
	 * @param   string $prefix   For the table - used to rename constraints in non-mysql databases
	 *
	 * @return  JDatabaseDriver    Returns this object to support chaining.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		$this->mark(__FUNCTION__);

		return $this;
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string $database The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function select($database)
	{
		$this->mark(__FUNCTION__);

		return true;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setUTF()
	{
		$this->mark(__FUNCTION__);

		return true;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @param   boolean $toSavepoint If true, commit to the last savepoint.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function transactionCommit($toSavepoint = false)
	{
		$this->mark(__FUNCTION__);
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @param   boolean $toSavepoint If true, rollback to the last savepoint.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function transactionRollback($toSavepoint = false)
	{
		$this->mark(__FUNCTION__);
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @param   boolean $asSavepoint If true and a transaction is already active, a savepoint will be created.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function transactionStart($asSavepoint = false)
	{
		$this->mark(__FUNCTION__);
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		$this->mark(__FUNCTION__);

		$this->lastQuery = (string) $this->getQuery();

		return true;
	}

	/**
	 * Unlocks tables in the database.
	 *
	 * @return  JDatabaseDriver  Returns this object to support chaining.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function unlockTables()
	{
		$this->mark(__FUNCTION__);

		return $this;
	}
}
