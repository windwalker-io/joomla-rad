<?php
/**
 * Part of joomla341c project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Handler;

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
		$this->onUpdate = $onUpdate;
		$this->onDelete = $onDelete;
		$this->field    = $field;
		$this->fks      = (array) $fks;
		$this->options  = $options;

		$this->tableName = $this->table->getTableName();
		$this->db = $table->getDbo();
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
