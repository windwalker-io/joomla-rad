<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Windwalker\Model\Helper\QueryHelper;
use Windwalker\Relation\Action;
use Windwalker\Table\Table;

/**
 * The AbstractRelationHandler class.
 *
 * @since  2.1
 */
abstract class AbstractRelationHandler implements RelationHandlerInterface
{
	/**
	 * Property parent.
	 *
	 * @var  Table
	 */
	protected $parent;

	/**
	 * Property table.
	 *
	 * @var  \JTable
	 */
	protected $table;

	/**
	 * Property onUpdate.
	 *
	 * @var  string
	 */
	protected $onUpdate;

	/**
	 * Property onDelete.
	 *
	 * @var  string
	 */
	protected $onDelete;

	/**
	 * Property fields.
	 *
	 * @var
	 */
	protected $field;

	/**
	 * Property fks.
	 *
	 * @var  array
	 */
	protected $fks;

	/**
	 * Property options.
	 *
	 * @var  array
	 */
	protected $options;

	/**
	 * Property tableName.
	 *
	 * @var  string
	 */
	protected $tableName;

	/**
	 * Property db.
	 *
	 * @var  \JDatabaseDriver
	 */
	protected $db;

	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix;

	/**
	 * Property flush.
	 *
	 * @var  boolean
	 */
	protected $flush = false;

	/**
	 * Class init.
	 *
	 * @param Table   $parent    The parent table od this relation.
	 * @param string  $field     Field of parent table to store children.
	 * @param \JTable $table     The Table object of this relation child.
	 * @param array   $fks       Foreign key mapping.
	 * @param string  $onUpdate  The action of ON UPDATE operation.
	 * @param string  $onDelete  The action of ON DELETE operation.
	 * @param array   $options   Some options to configure this relation.
	 */
	public function __construct($parent, $field = null, $table = null, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		$this->targetTable($table, $fks);

		$this->parent   = $parent;
		$this->onUpdate = $onUpdate ? : Action::CASCADE;
		$this->onDelete = $onDelete ? : Action::CASCADE;
		$this->field    = $field;
		$this->options  = $options;
		$this->flush    = $this->getOption('flush', $this->flush);

		$this->db = $this->db ? : \JFactory::getDbo();
	}

	/**
	 * Get value from parent relative field.
	 *
	 * @return  mixed
	 */
	public function getParentFieldValue()
	{
		return $this->parent->{$this->field};
	}

	/**
	 * Set value to parent relative field.
	 *
	 * @param   mixed  $value  The value to set.
	 *
	 * @return  static
	 */
	public function setParentFieldValue($value)
	{
		$this->parent->{$this->field} = $value;

		return $this;
	}

	/**
	 * deleteAllRelatives
	 *
	 * @return  static
	 */
	public function deleteAllRelatives()
	{
		$query = $this->db->getQuery(true);

		foreach ($this->fks as $field => $foreign)
		{
			$query->where($query->format('%n = %q', $foreign, $this->parent->$field));
		}

		$query->delete($this->table->getTableName());

		$this->db->setQuery($query)->execute();

		return $this;
	}

	/**
	 * Handle update relation and set matched value to child table.
	 *
	 * @param   \JTable  $itemTable  The child table to be handled.
	 *
	 * @return  \JTable  Return table if you need.
	 */
	public function handleUpdateRelations(\JTable $itemTable)
	{
		// Handle Cascade
		if ($this->onUpdate == Action::CASCADE)
		{
			$itemTable = $this->syncParentFields($itemTable);
		}
		// Handle Set NULL
		elseif ($this->onUpdate == Action::SET_NULL)
		{
			if ($this->changed($itemTable))
			{
				$itemTable = $this->setRelativeFields($itemTable, null);
			}
		}

		return $itemTable;
	}

	/**
	 * Handle delete relation, if is CASCADE, mark child table to delete. If is SET NULL, set all children fields to NULL.
	 *
	 * @param   \JTable  $itemTable  The child table to be handled.
	 *
	 * @return  \JTable  Return table if you need.
	 */
	public function handleDeleteRelations(\JTable $itemTable)
	{
		// Handle Cascade
		if ($this->onDelete == Action::CASCADE)
		{
			$itemTable->_delete = true;
		}
		// Handle Set NULL
		elseif ($this->onDelete == Action::SET_NULL)
		{
			$itemTable = $this->setRelativeFields($itemTable, null);
		}

		return $itemTable;
	}

	/**
	 * Sync parent fields value to child table.
	 *
	 * @param   \JTable  $itemTable  The child table to be handled.
	 *
	 * @return  \JTable  Return table if you need.
	 */
	protected function syncParentFields(\JTable $itemTable)
	{
		foreach ($this->fks as $field => $foreign)
		{
			$itemTable->$foreign = $this->parent->$field;
		}

		return $itemTable;
	}

	/**
	 * Set value to all relative children fields.
	 *
	 * @param   \JTable  $itemTable  The child table to be handled.
	 * @param   mixed    $value      The value we want to set to child, default is NULL.
	 *
	 * @return  \JTable  Return table if you need.
	 */
	protected function setRelativeFields(\JTable $itemTable, $value = null)
	{
		foreach ($this->fks as $field => $foreign)
		{
			$itemTable->$foreign = $value;
		}

		return $itemTable;
	}

	/**
	 * Is fields changed. If any field changed, means we have to do something to children.
	 *
	 * @param   \JTable  $itemTable  The child table to be handled.
	 *
	 * @return  boolean  Something changed of not.
	 */
	public function changed($itemTable)
	{
		// If any key changed, set all fields as NULL.
		foreach ($this->fks as $field => $foreign)
		{
			if ($itemTable->$foreign != $this->parent->$field)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Convert all data type to Table object.
	 *
	 * @param   mixed  $item  The data to be converted.
	 *
	 * @return  \JTable  Return Converted Table object.
	 */
	public function convertToTable($item)
	{
		$table = clone $this->table;
		$table->reset();

		if (!($item instanceof \JTable))
		{
			if ($item instanceof Data)
			{
				$item = $item->dump();
			}
			elseif ($item instanceof \Traversable)
			{
				$item = iterator_to_array($item);
			}

			$itemTable = clone $table;
			$itemTable->bind((array) $item);
		}
		else
		{
			$itemTable = $item;
		}

		return $itemTable;
	}

	/**
	 * Convert all data type to Windwalker Data object.
	 *
	 * @param   mixed  $item  The data to be converted.
	 *
	 * @return  Data  Return Converted Data object.
	 */
	public function convertToData($item)
	{
		if ($item instanceof \JTable)
		{
			$item = $item->getProperties();
		}
		elseif ($item instanceof \Traversable)
		{
			$item = iterator_to_array($item);
		}

		return new Data($item);
	}

	/**
	 * Convert all data set to Windwalker DataSet object.
	 *
	 * @param   object[]  $items  The data set to be converted.
	 *
	 * @return  DataSet  Return Converted DataSet object.
	 */
	public function convertToDataSet($items)
	{
		$dataset = new DataSet;

		foreach ($items as $item)
		{
			$dataset[] = $this->convertToData($item);
		}

		return $dataset;
	}

	/**
	 * clearPrimaryKeys
	 *
	 * @param \JTable $itemTable
	 *
	 * @return  \JTable
	 */
	public function clearPrimaryKeys(\JTable $itemTable)
	{
		foreach ($itemTable->getKeyName(true) as $key)
		{
			$itemTable->$key = null;
		}

		return $itemTable;
	}

	/**
	 * Build query for load operation.
	 *
	 * @param   \JDatabaseQuery  $query  The query object to handle.
	 *
	 * @return  \JDatabaseQuery  Return handled query object.
	 */
	public function buildLoadQuery(\JDatabaseQuery $query = null)
	{
		$conditions = array();

		foreach ($this->fks as $field => $foreign)
		{
			$conditions[$foreign] = $this->parent->$field;
		}

		$query = $query ? : $this->db->getQuery(true);

		QueryHelper::buildWheres($query, $conditions);

		$query->select('*')
			->from($this->tableName);

		return $query;
	}

	/**
	 * Method to get property Prefix
	 *
	 * @return  string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * Method to set property prefix
	 *
	 * @param   string $prefix
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;

		return $this;
	}

	/**
	 * Get Table object.
	 *
	 * @param   string  $name    The table name.
	 * @param   string  $prefix  The table class prefix.
	 *
	 * @return  \JTable
	 */
	protected function getTable($name, $prefix = null)
	{
		if (!is_string($name))
		{
			throw new \InvalidArgumentException('Table name should be string.');
		}

		if ($table = \JTable::getInstance($name, $prefix ? : $this->prefix))
		{
			return $table;
		}

		return new Table($name);
	}

	/**
	 * Method to get property Parent
	 *
	 * @return  Table
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Method to set property parent
	 *
	 * @param   Table $parent
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function parent($parent)
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * Method to get property Table
	 *
	 * @return  \JTable
	 */
	public function getTarget()
	{
		return $this->table;
	}

	/**
	 * Method to set property table
	 *
	 * @param   \JTable $table
	 * @param   array   $fks
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function targetTable($table, $fks)
	{
		if (!$table)
		{
			return $this;
		}

		if (!($table instanceof \JTable))
		{
			$table = $this->getTable($table, $this->prefix);
		}

		$this->table = $table;
		$this->foreignKeys($fks);

		$this->tableName = $this->table->getTableName();
		$this->db = $table->getDbo();

		return $this;
	}

	/**
	 * Method to get property OnUpdate
	 *
	 * @return  string
	 */
	public function getOnUpdate()
	{
		return $this->onUpdate;
	}

	/**
	 * Method to set property onUpdate
	 *
	 * @param   string $onUpdate
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function onUpdate($onUpdate)
	{
		$this->onUpdate = $onUpdate;

		return $this;
	}

	/**
	 * Method to get property OnDelete
	 *
	 * @return  string
	 */
	public function getOnDelete()
	{
		return $this->onDelete;
	}

	/**
	 * Method to set property onDelete
	 *
	 * @param   string $onDelete
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function onDelete($onDelete)
	{
		$this->onDelete = $onDelete;

		return $this;
	}

	/**
	 * Method to get property Field
	 *
	 * @return  mixed
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Method to set property field
	 *
	 * @param   mixed $field
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function field($field)
	{
		$this->field = $field;

		return $this;
	}

	/**
	 * Method to get property Fks
	 *
	 * @return  array
	 */
	public function getForeignKeys()
	{
		return $this->fks;
	}

	/**
	 * Method to set property fks
	 *
	 * @param   array $fks
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function foreignKeys($fks)
	{
		if (!is_array($fks))
		{
			throw new \InvalidArgumentException('Argument $fks should be array');
		}

		$this->fks = $fks;

		return $this;
	}

	/**
	 * getOption
	 *
	 * @param string $name
	 * @param mixed  $default
	 *
	 * @return  mixed
	 */
	public function getOption($name, $default = null)
	{
		if (empty($this->options[$name]))
		{
			return $default;
		}

		return $this->options[$name];
	}

	/**
	 * setOption
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return  static
	 */
	public function setOption($name, $value)
	{
		$this->options[$name] = $value;

		return $this;
	}

	/**
	 * Method to get property Options
	 *
	 * @return  array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Method to set property options
	 *
	 * @param   array $options
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setOptions($options)
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * Method to get property TableName
	 *
	 * @return  string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * Method to set property tableName
	 *
	 * @param   string $tableName
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setTableName($tableName)
	{
		$this->tableName = $tableName;

		return $this;
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

	/**
	 * Method to get property Flush
	 *
	 * @return  boolean
	 */
	public function getFlush()
	{
		return $this->flush;
	}

	/**
	 * Method to set property flush
	 *
	 * @param   boolean $flush
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function flush($flush)
	{
		$this->flush = $flush;

		return $this;
	}

	/**
	 * Method to set property parent
	 *
	 * @param   Table $parent
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;

		return $this;
	}
}
