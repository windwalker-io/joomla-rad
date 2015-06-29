<?php
/**
 * Part of joomla341c project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Relation\Action;

/**
 * The AbstractRelationHandler class.
 *
 * @since  {DEPLOY_VERSION}
 */
abstract class AbstractRelationHandler implements RelationHandlerInterface
{
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
	 * @param string  $field
	 * @param \JTable $table
	 * @param array   $fks
	 * @param string  $onUpdate
	 * @param string  $onDelete
	 * @param array   $options
	 */
	public function __construct($field, $table, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		$this->table    = $table;
		$this->onUpdate = $onUpdate;
		$this->onDelete = $onDelete;
		$this->field   = $field;
		$this->fks      = (array) $fks;
		$this->options  = $options;

		$this->tableName = $this->table->getTableName();
		$this->db = $table->getDbo();
	}
}
