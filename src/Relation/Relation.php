<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation;

use Windwalker\Relation\Handler\ManyToManyRelation;
use Windwalker\Relation\Handler\ManyToOneRelation;
use Windwalker\Relation\Handler\OneToManyRelation;
use Windwalker\Relation\Handler\OneToOneRelation;
use Windwalker\Relation\Handler\RelationHandlerInterface;
use Windwalker\Table\Table;

/**
 * The Relation handler object. This is a composite object to combine multiple relation handlers.
 * 
 * @since  {DEPLOY_VERSION}
 */
class Relation implements RelationHandlerInterface
{
	/**
	 * Property parent.
	 *
	 * @var  Table
	 */
	protected $parent;

	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix;

	/**
	 * Property relations.
	 *
	 * @var  RelationHandlerInterface[]
	 */
	protected $relations = array();

	/**
	 * Class init.
	 *
	 * @param Table  $parent
	 * @param string $prefix
	 */
	public function __construct(Table $parent, $prefix = 'JTable')
	{
		$this->parent = $parent;
	}

	/**
	 * Add one to many relation configurations.
	 *
	 * @param string  $field     Field of parent table to store children.
	 * @param \JTable $table     The Table object of this relation child.
	 * @param array   $fks       Foreign key mapping.
	 * @param string  $onUpdate  The action of ON UPDATE operation.
	 * @param string  $onDelete  The action of ON DELETE operation.
	 * @param array   $options   Some options to configure this relation.
	 *
	 * @return  static
	 */
	public function addOneToMany($field, $table, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		$relation = new OneToManyRelation($this->parent, $field, $table, $fks, $onUpdate, $onDelete, $options);

		$relation->parent($this->parent)->setPrefix($this->prefix);

		$this->relations[$field] = $relation;

		return $relation;
	}

	/**
	 * Add one to many relation configurations.
	 *
	 * @param string  $field    Field of parent table to store children.
	 * @param \JTable $map      The mapping table.
	 * @param array   $mapFks   The mapping foreign keys.
	 * @param \JTable $table    The Table object of this relation child.
	 * @param array   $fks      Foreign key mapping.
	 * @param string  $onUpdate The action of ON UPDATE operation.
	 * @param string  $onDelete The action of ON DELETE operation.
	 * @param array   $options  Some options to configure this relation.
	 *
	 * @return ManyToManyRelation
	 */
	public function addManyToMany($field, $map = null, $mapFks = array(), $table = null, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		$relation = new ManyToManyRelation($this->parent, $field, $map, $mapFks, $table, $fks, $onUpdate, $onDelete, $options);

		$relation->parent($this->parent)->setPrefix($this->prefix);

		$this->relations[$field] = $relation;

		return $relation;
	}

	/**
	 * Add one to many relation configurations.
	 *
	 * @param string  $field     Field of parent table to store children.
	 * @param \JTable $table     The Table object of this relation child.
	 * @param array   $fks       Foreign key mapping.
	 * @param string  $onUpdate  The action of ON UPDATE operation.
	 * @param string  $onDelete  The action of ON DELETE operation.
	 * @param array   $options   Some options to configure this relation.
	 *
	 * @return  static
	 */
	public function addManyToOne($field, $table, $fks = array(), $onUpdate = Action::NO_ACTION, $onDelete = Action::NO_ACTION,
		$options = array())
	{
		$relation = new ManyToOneRelation($this->parent, $field, $table, $fks, $onUpdate, $onDelete, $options);

		$relation->parent($this->parent)->setPrefix($this->prefix);

		$this->relations[$field] = $relation;

		return $relation;
	}

	/**
	 * Add one to many relation configurations.
	 *
	 * @param string  $field     Field of parent table to store children.
	 * @param \JTable $table     The Table object of this relation child.
	 * @param array   $fks       Foreign key mapping.
	 * @param string  $onUpdate  The action of ON UPDATE operation.
	 * @param string  $onDelete  The action of ON DELETE operation.
	 * @param array   $options   Some options to configure this relation.
	 *
	 * @return  static
	 */
	public function addOneToOne($field, $table, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		$relation = new OneToOneRelation($this->parent, $field, $table, $fks, $onUpdate, $onDelete, $options);

		$relation->parent($this->parent)->setPrefix($this->prefix);

		$this->relations[$field] = $relation;

		return $relation;
	}

	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{
		foreach ($this->relations as $relation)
		{
			$relation->load();
		}
	}

	/**
	 * Store all relative children data.
	 *
	 * The onUpdate option will work in this method.
	 *
	 * @return  void
	 */
	public function store()
	{
		foreach ($this->relations as $relation)
		{
			$relation->store();
		}
	}

	/**
	 * Delete all relative children data.
	 *
	 * The onDelete option will work in this method.
	 *
	 * @return  void
	 */
	public function delete()
	{
		foreach ($this->relations as $relation)
		{
			$relation->delete();
		}
	}

	/**
	 * Get Table object.
	 *
	 * @param   string  $table   The table name.
	 * @param   string  $prefix  The table class prefix.
	 *
	 * @return  \JTable
	 */
	protected function getTable($table, $prefix = null)
	{
		if (!is_string($table))
		{
			throw new \InvalidArgumentException('Table name should be string.');
		}

		if ($table = \JTable::getInstance($table, $prefix ? : $this->prefix))
		{
			return $table;
		}

		return new Table($table, 'id', $this->parent->getDbo());
	}
}
