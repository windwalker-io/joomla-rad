<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Windwalker\Relation\Action;
use Windwalker\Table\Table;

/**
 * The AbstractRelationHandler class.
 *
 * @since  {DEPLOY_VERSION}
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
	public function __construct($parent, $field, $table, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		$this->parent   = $parent;
		$this->table    = $table;
		$this->onUpdate = $onUpdate ? : Action::CASCADE;
		$this->onDelete = $onDelete ? : Action::CASCADE;
		$this->field    = $field;
		$this->fks      = (array) $fks;
		$this->options  = $options;

		$this->tableName = $this->table->getTableName();
		$this->db = $table->getDbo();
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
		$changed = false;

		// If any key changed, set all fields as NULL.
		foreach ($this->fks as $field => $foreign)
		{
			if ($itemTable->$foreign != $this->parent->$field)
			{
				$changed = true;

				break;
			}
		}

		return $changed;
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
	public function setParent($parent)
	{
		$this->parent = $parent;

		return $this;
	}
}
