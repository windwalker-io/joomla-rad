<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation\Observer;

use JObservableInterface;
use JObserverInterface;
use Windwalker\Table\Table;

/**
 * The RelationObserver class.
 * 
 * @since  {DEPLOY_VERSION}
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
	 * onAfterLoad
	 *
	 * @param bool  $result
	 * @param array $row
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
	 * onAfterStore
	 *
	 * @param boolean $result
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
	 * onBeforeDelete
	 *
	 * @param mixed $pk
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
	 * onAfterDelete
	 *
	 * @param mixed $pk
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
