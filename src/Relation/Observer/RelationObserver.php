<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation\Observer;

use JObservableInterface;
use JObserverInterface;
use Windwalker\DI\Container;
use Windwalker\Relation\Handler\AbstractRelationHandler;
use Windwalker\Relation\Relation;
use Windwalker\Table\Table;

/**
 * The RelationObserver class.
 * 
 * @since  2.1
 */
class RelationObserver extends \JTableObserver
{
	/**
	 * Property tableClone.
	 *
	 * @var  Table
	 */
	protected $tableClone;

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 *
	 * @param   JObservableInterface $observableObject The observable subject object
	 * @param   array                $params           Params for this observer
	 *
	 * @return  JObserverInterface
	 *
	 * @since   3.1.2
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new static($observableObject);

		return $observer;
	}

	/**
	 * Pass Relation object to Parent Table.
	 *
	 * @return  void
	 */
	public function onAfterConstruction()
	{
		if (!($this->table instanceof Table))
		{
			return;
		}

		// Pass global relation configuration into parent.
		$relations = Container::getInstance()->get('relation.container')->getRelation($this->table->getTableName());

		/** @var Relation $relations */
		foreach ($relations->getRelations() as $relation)
		{
			/** @var AbstractRelationHandler $relation */
			$relation->setParent($this->table);

			$this->table->_relation->setRelation($relation->getField(), $relation);
		}
	}

	/**
	 * Event after load.
	 *
	 * @param  boolean  $result  Load result.
	 * @param  array    $row     The found data.
	 *
	 * @return  void
	 */
	public function onAfterLoad(&$result, $row)
	{
		if ($this->table instanceof Table && $result)
		{
			$this->table->_relation->load();
		}
	}

	/**
	 * Event after store.
	 *
	 * @param  boolean  $result  The store result.
	 *
	 * @return  void
	 */
	public function onAfterStore(&$result)
	{
		if ($this->table instanceof Table && $result)
		{
			$this->table->_relation->store();
		}
	}

	/**
	 * Event before  delete.
	 *
	 * @param   mixed  $pk  The delete key or conditions.
	 *
	 * @return  void
	 */
	public function onBeforeDelete($pk)
	{
		if ($this->table instanceof Table)
		{
			$this->tableClone = clone $this->table;

			$this->tableClone->load($pk);
		}
	}

	/**
	 * Event after delete.
	 *
	 * @param   mixed  $pk  The delete key or conditions.
	 *
	 * @return  void
	 */
	public function onAfterDelete($pk)
	{
		if ($this->table instanceof Table)
		{
			$this->tableClone->_relation->delete();
		}
	}
}
