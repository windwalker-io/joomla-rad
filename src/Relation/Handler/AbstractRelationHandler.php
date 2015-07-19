<?php
/**
 * Part of joomla341c project.
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
	 * @param Table   $parent
	 * @param string  $field
	 * @param \JTable $table
	 * @param array   $fks
	 * @param string  $onUpdate
	 * @param string  $onDelete
	 * @param array   $options
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
	 * handleRelations
	 *
	 * @param \JTable $itemTable
	 *
	 * @return  \JTable
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
	 * handleDeleteRelations
	 *
	 * @param \JTable $itemTable
	 *
	 * @return  \JTable
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
	 * syncParentFields
	 *
	 * @param \JTable $itemTable
	 *
	 * @return  \JTable
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
	 * setRelativeFields
	 *
	 * @param \JTable $itemTable
	 * @param mixed   $value
	 *
	 * @return  \JTable
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
	 * changed
	 *
	 * @param \JTable $itemTable
	 *
	 * @return  boolean
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
	 * convertToTable
	 *
	 * @param  mixed  $item
	 *
	 * @return  \JTable
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
	 * convertToData
	 *
	 * @param   mixed  $item
	 *
	 * @return  Data
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
	 * convertToDataSet
	 *
	 * @param   object[]  $items
	 *
	 * @return  DataSet
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
