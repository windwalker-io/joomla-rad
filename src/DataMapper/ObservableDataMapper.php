<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\DataMapper;

use JObserverInterface;
use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Windwalker\DataMapper\Adapter\DatabaseAdapterInterface;
use Windwalker\DataMapper\Observer\RelationObserver;
use Windwalker\Joomla\Database\JoomlaAdapter;
use Windwalker\Relation\Relation;
use Windwalker\Table\Table;

/**
 * An observable DataMapper, we can add observers to this object and trigger them when running CRUD.
 *
 * @property-read  Relation  $relation  The Relation object.
 * 
 * @since  2.1
 */
class ObservableDataMapper extends DataMapper implements \JObservableInterface
{
	/**
	 * Property observers.
	 *
	 * @var  \JObserverUpdater
	 */
	protected $observers;

	/**
	 * Property db.
	 *
	 * @var  JoomlaAdapter
	 */
	protected $db;

	/**
	 * Property relation.
	 *
	 * @var  Relation
	 */
	protected $relation;

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

		$this->relation = new Relation(new Table($this->table, $this->pk, $this->db->getDb()));

		$this->attachObserver(new RelationObserver($this));

		$this->observers->update('onAfterConstruction', array());
	}

	/**
	 * Find records and return data set.
	 *
	 * Example:
	 * - `$mapper->find(array('id' => 5), 'date', 20, 10);`
	 * - `$mapper->find(null, 'id', 0, 1);`
	 *
	 * @param   mixed    $conditions Where conditions, you can use array or Compare object.
	 *                               Example:
	 *                               - `array('id' => 5)` => id = 5
	 *                               - `new GteCompare('id', 20)` => 'id >= 20'
	 *                               - `new Compare('id', '%Flower%', 'LIKE')` => 'id LIKE "%Flower%"'
	 * @param   mixed    $order      Order sort, can ba string, array or object.
	 *                               Example:
	 *                               - `id ASC` => ORDER BY id ASC
	 *                               - `array('catid DESC', 'id')` => ORDER BY catid DESC, id
	 * @param   integer  $start      Limit start number.
	 * @param   integer  $limit      Limit rows.
	 *
	 * @return  mixed|DataSet Found rows data set.
	 * @since   2.0
	 */
	public function find($conditions = array(), $order = null, $start = null, $limit = null)
	{
		$this->observers->update('onBeforeFind', array($conditions, $order, $start, $limit));

		$dataset = parent::find($conditions, $order, $start, $limit);

		$this->observers->update('onAfterFind', array(&$dataset));

		return $dataset;
	}

	/**
	 * Find records without where conditions and return data set.
	 *
	 * Same as `$mapper->find(null, 'id', $start, $limit);`
	 *
	 * @param mixed   $order Order sort, can ba string, array or object.
	 *                       Example:
	 *                       - 'id ASC' => ORDER BY id ASC
	 *                       - array('catid DESC', 'id') => ORDER BY catid DESC, id
	 * @param integer $start Limit start number.
	 * @param integer $limit Limit rows.
	 *
	 * @return mixed|DataSet Found rows data set.
	 */
	public function findAll($order = null, $start = null, $limit = null)
	{
		$this->observers->update('onBeforeFindAll', array($order, $start, $limit));

		$dataset = parent::findAll($order, $start, $limit);

		$this->observers->update('onAfterFindAll', array(&$dataset));

		return $dataset;
	}

	/**
	 * Find one record and return a data.
	 *
	 * Same as `$mapper->find($conditions, 'id', 0, 1);`
	 *
	 * @param mixed $conditions Where conditions, you can use array or Compare object.
	 *                          Example:
	 *                          - `array('id' => 5)` => id = 5
	 *                          - `new GteCompare('id', 20)` => 'id >= 20'
	 *                          - `new Compare('id', '%Flower%', 'LIKE')` => 'id LIKE "%Flower%"'
	 * @param mixed $order      Order sort, can ba string, array or object.
	 *                          Example:
	 *                          - `id ASC` => ORDER BY id ASC
	 *                          - `array('catid DESC', 'id')` => ORDER BY catid DESC, id
	 *
	 * @return mixed|Data Found row data.
	 */
	public function findOne($conditions = array(), $order = null)
	{
		$this->observers->update('onBeforeFindOne', array($conditions, $order));

		$data = parent::findOne($conditions, $order);

		$this->observers->update('onAfterFindOne', array(&$data));

		return $data;
	}

	/**
	 * Find column as an array.
	 *
	 * @param string  $column     The column we want to select.
	 * @param mixed   $conditions Where conditions, you can use array or Compare object.
	 *                            Example:
	 *                            - `array('id' => 5)` => id = 5
	 *                            - `new GteCompare('id', 20)` => 'id >= 20'
	 *                            - `new Compare('id', '%Flower%', 'LIKE')` => 'id LIKE "%Flower%"'
	 * @param mixed   $order      Order sort, can ba string, array or object.
	 *                            Example:
	 *                            - `id ASC` => ORDER BY id ASC
	 *                            - `array('catid DESC', 'id')` => ORDER BY catid DESC, id
	 * @param integer $start      Limit start number.
	 * @param integer $limit      Limit rows.
	 *
	 * @return  mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function findColumn($column, $conditions = array(), $order = null, $start = null, $limit = null)
	{
		$this->observers->update('onBeforeFindColumn', array($column, $conditions, $order, $start, $limit));

		$columns = parent::findColumn($column, $conditions, $order, $start, $limit);

		$this->observers->update('onAfterFindColumn', array(&$columns));

		return $columns;
	}

	/**
	 * Create records by data set.
	 *
	 * @param mixed $dataset The data set contains data we want to store.
	 *
	 * @throws \UnexpectedValueException
	 * @throws \InvalidArgumentException
	 * @return  mixed|DataSet  Data set data with inserted id.
	 */
	public function create($dataset)
	{
		$this->observers->update('onBeforeCreate', array(&$dataset));

		$dataset = parent::create($dataset);

		$this->observers->update('onAfterCreate', array(&$dataset));

		return $dataset;
	}

	/**
	 * Create one record by data object.
	 *
	 * @param mixed $data Send a data in and store.
	 *
	 * @throws \InvalidArgumentException
	 * @return  mixed|Data Data with inserted id.
	 */
	public function createOne($data)
	{
		$this->observers->update('onBeforeCreateOne', array(&$data));

		$data = parent::createOne($data);

		$this->observers->update('onAfterCreateOne', array(&$data));

		return $data;
	}

	/**
	 * Update records by data set. Every data depend on this table's primary key to update itself.
	 *
	 * @param mixed $dataset      Data set contain data we want to update.
	 * @param array $condFields   The where condition tell us record exists or not, if not set,
	 *                            will use primary key instead.
	 * @param bool  $updateNulls  Update empty fields or not.
	 *
	 * @return mixed|DataSet
	 */
	public function update($dataset, $condFields = null, $updateNulls = false)
	{
		$this->observers->update('onBeforeUpdate', array(&$dataset, &$condFields, &$updateNulls));

		$dataset = parent::update($dataset, $condFields, $updateNulls);

		$this->observers->update('onAfterUpdate', array(&$dataset));

		return $dataset;
	}

	/**
	 * Same as update(), just update one row.
	 *
	 * @param mixed $data         The data we want to update.
	 * @param array $condFields   The where condition tell us record exists or not, if not set,
	 *                            will use primary key instead.
	 * @param bool  $updateNulls  Update empty fields or not.
	 *
	 * @return mixed|Data
	 */
	public function updateOne($data, $condFields = null, $updateNulls = false)
	{
		$this->observers->update('onBeforeUpdateOne', array(&$data, &$condFields, &$updateNulls));

		$data = parent::updateOne($data, $condFields, $updateNulls);

		$this->observers->update('onAfterUpdateOne', array(&$data));

		return $data;
	}

	/**
	 * Using one data to update multiple rows, filter by where conditions.
	 * Example:
	 * `$mapper->updateAll(new Data(array('published' => 0)), array('date' => '2014-03-02'))`
	 * Means we make every records which date is 2014-03-02 unpublished.
	 *
	 * @param mixed $data       The data we want to update to every rows.
	 * @param mixed $conditions Where conditions, you can use array or Compare object.
	 *                          Example:
	 *                          - `array('id' => 5)` => id = 5
	 *                          - `new GteCompare('id', 20)` => 'id >= 20'
	 *                          - `new Compare('id', '%Flower%', 'LIKE')` => 'id LIKE "%Flower%"'
	 *
	 * @throws \InvalidArgumentException
	 * @return  boolean
	 */
	public function updateAll($data, $conditions = array())
	{
		$this->observers->update('onBeforeUpdateAll', array(&$data, &$conditions));

		$result = parent::updateAll($data, $conditions);

		$this->observers->update('onAfterUpdateAll', array(&$result));

		return $result;
	}

	/**
	 * Flush records, will delete all by conditions then recreate new.
	 *
	 * @param mixed $dataset    Data set contain data we want to update.
	 * @param mixed $conditions Where conditions, you can use array or Compare object.
	 *                          Example:
	 *                          - `array('id' => 5)` => id = 5
	 *                          - `new GteCompare('id', 20)` => 'id >= 20'
	 *                          - `new Compare('id', '%Flower%', 'LIKE')` => 'id LIKE "%Flower%"'
	 *
	 * @return  mixed|DataSet Updated data set.
	 */
	public function flush($dataset, $conditions = array())
	{
		$this->observers->update('onBeforeFlush', array(&$dataset, &$conditions));

		$dataset = parent::flush($dataset, $conditions);

		$this->observers->update('onAfterFlush', array(&$dataset));

		return $dataset;
	}

	/**
	 * Save will auto detect is conditions matched in data or not.
	 * If matched, using update, otherwise we will create it as new record.
	 *
	 * @param mixed $dataset      The data set contains data we want to save.
	 * @param array $condFields   The where condition tell us record exists or not, if not set,
	 *                            will use primary key instead.
	 * @param bool  $updateNulls  Update empty fields or not.
	 *
	 * @return  mixed|DataSet Saved data set.
	 */
	public function save($dataset, $condFields = null, $updateNulls = false)
	{
		$this->observers->update('onBeforeSave', array(&$dataset, &$condFields, &$updateNulls));

		$dataset = parent::save($dataset, $condFields, $updateNulls);

		$this->observers->update('onAfterSave', array(&$dataset));

		return $dataset;
	}

	/**
	 * Save only one row.
	 *
	 * @param mixed $data         The data we want to save.
	 * @param array $condFields   The where condition tell us record exists or not, if not set,
	 *                            will use primary key instead.
	 * @param bool  $updateNulls  Update empty fields or not.
	 *
	 * @return  mixed|Data Saved data.
	 */
	public function saveOne($data, $condFields = null, $updateNulls = false)
	{
		$this->observers->update('onBeforeSaveOne', array(&$data, &$condFields, &$updateNulls));

		$data = parent::saveOne($data, $condFields, $updateNulls);

		$this->observers->update('onAfterSaveOne', array(&$data));

		return $data;
	}

	/**
	 * Delete records by where conditions.
	 *
	 * @param mixed   $conditions Where conditions, you can use array or Compare object.
	 *                            Example:
	 *                            - `array('id' => 5)` => id = 5
	 *                            - `new GteCompare('id', 20)` => 'id >= 20'
	 *                            - `new Compare('id', '%Flower%', 'LIKE')` => 'id LIKE "%Flower%"'
	 *
	 * @return  boolean Will be always true.
	 */
	public function delete($conditions)
	{
		$this->observers->update('onBeforeDelete', array(&$conditions));

		$result = parent::delete($conditions);

		$this->observers->update('onAfterDelete', array(&$result));

		return $result;
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

	/**
	 * Adds an observer to this JObservableInterface instance.
	 * Ideally, this method should be called fron the constructor of JObserverInterface
	 * which should be instanciated by JObserverMapper.
	 * The implementation of this function can use JObserverUpdater
	 *
	 * @param   JObserverInterface $observer The observer to attach to $this observable subject
	 *
	 * @return  void
	 */
	public function attachObserver(JObserverInterface $observer)
	{
		$this->observers->attachObserver($observer);
	}

	/**
	 * Magic method to get protected property.
	 *
	 * @param string $name
	 *
	 * @return  mixed
	 */
	public function __get($name)
	{
		if ($name === 'relation')
		{
			return $this->$name;
		}

		throw new \UnexpectedValueException('Property ' . $name . ' not exists or protected.');
	}

	/**
	 * Method to get property Relation
	 *
	 * @return  Relation
	 */
	public function getRelation()
	{
		return $this->relation;
	}

	/**
	 * Method to set property relation
	 *
	 * @param   Relation $relation
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setRelation($relation)
	{
		$this->relation = $relation;

		return $this;
	}
}
