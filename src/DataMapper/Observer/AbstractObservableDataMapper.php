<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\DataMapper\Observer;

use Windwalker\DataMapper\Adapter\DatabaseAdapterInterface;
use Windwalker\DataMapper\DataMapper;

/**
 * The AbstractObserverableDataMapper class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class AbstractObservableDataMapper extends DataMapper
{
	/**
	 * Property observers.
	 *
	 * @var  \JObserverUpdater
	 */
	protected $observers;

	/**
	 * Constructor.
	 *
	 * @param   string                    $table  Table name.
	 * @param   string|array              $pk     Primary key.
	 * @param   DatabaseAdapterInterface  $db     Database adapter.
	 */
	public function __construct($table = null, $pk = 'id', DatabaseAdapterInterface $db = null)
	{
		// Implement JObservableInterface:
		// Create observer updater and attaches all observers interested by $this class:
		$this->observers = new \JObserverUpdater($this);
		\JObserverMapper::attachAllObservers($this);

		parent::__construct($table, $pk, $db);

		$this->observers->update('onAfterConstruction', array());
	}

	public function find($conditions = array(), $order = null, $start = null, $limit = null)
	{
		$this->observers->update('onBeforeFind', array($conditions, $order, $start, $limit));

		$dataset = parent::find($conditions, $order, $start, $limit);

		$this->observers->update('onAfterFind', array($dataset));

		return $dataset;
	}

	public function findAll($order = null, $start = null, $limit = null)
	{
		$this->observers->update('onBeforeFindAll', array($order, $start, $limit));

		$dataset = parent::findAll($order, $start, $limit);

		$this->observers->update('onAfterFindAll', array($dataset));

		return $dataset;
	}

	public function findOne($conditions = array(), $order = null)
	{
		$this->observers->update('onBeforeFindOne', array($conditions, $order));

		$data = parent::findOne($conditions, $order);

		$this->observers->update('onAfterFindOne', array($data));

		return $data;
	}

	public function findColumn($column, $conditions = array(), $order = null, $start = null, $limit = null)
	{
		$this->observers->update('onBeforeFindColumn', array($column, $conditions, $order, $start, $limit));

		$columns = parent::findColumn($column, $conditions, $order, $start, $limit);

		$this->observers->update('onAfterFindColumn', array($columns));

		return $columns;
	}

	public function create($dataset)
	{
		return parent::create($dataset);
	}

	public function createOne($data)
	{
		return parent::createOne($data);
	}

	public function update($dataset, $condFields = null, $updateNulls = false)
	{
		return parent::update($dataset, $condFields, $updateNulls);
	}

	public function updateOne($data, $condFields = null, $updateNulls = false)
	{
		return parent::updateOne($data, $condFields, $updateNulls);
	}

	public function updateAll($data, $conditions = array())
	{
		return parent::updateAll($data, $conditions);
	}

	public function flush($dataset, $conditions = array())
	{
		return parent::flush($dataset, $conditions);
	}

	public function save($dataset, $condFields = null, $updateNulls = false)
	{
		return parent::save($dataset, $condFields, $updateNulls);
	}

	public function saveOne($data, $condFields = null, $updateNulls = false)
	{
		return parent::saveOne($data, $condFields, $updateNulls);
	}

	public function delete($conditions)
	{
		return parent::delete($conditions);
	}

	/**
	 * Method to get property Observers
	 *
	 * @return  \JObserverUpdater
	 */
	public function getObservers()
	{
		return $this->observers;
	}

	/**
	 * Method to set property observers
	 *
	 * @param   \JObserverUpdater $observers
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setObservers($observers)
	{
		$this->observers = $observers;

		return $this;
	}
}
