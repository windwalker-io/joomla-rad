<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\DataMapper\Proxy;

use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Windwalker\DataMapper\AbstractDataMapper;
use Windwalker\DataMapper\DataMapper;
use Windwalker\DataMapper\ObservableDataMapper;
use Windwalker\DataMapper\Observer\AbstractDataMapperObserver;

/**
 * The AbstractDataMapperFacade class.
 *
 * @see  DataMapper
 * @see  AbstractDataMapper
 *
 * @method  static  DataSet|Data[]  find($conditions = array(), $order = null, $start = null, $limit = null)
 * @method  static  DataSet|Data[]  findAll($order = null, $start = null, $limit = null)
 * @method  static  Data            findOne($conditions = array(), $order = null)
 * @method  static  array           findColumn($column, $conditions = array(), $order = null, $start = null, $limit = null)
 * @method  static  DataSet|Data[]  create($dataset)
 * @method  static  Data            createOne($data)
 * @method  static  DataSet|Data[]  update($dataset, $condFields = null, $updateNulls = false)
 * @method  static  Data            updateOne($data, $condFields = null, $updateNulls = false)
 * @method  static  boolean         updateAll($data, $conditions = array())
 * @method  static  DataSet|Data[]  flush($dataset, $conditions = array())
 * @method  static  DataSet|Data[]  save($dataset, $condFields = null, $updateNulls = false)
 * @method  static  Data            saveOne($data, $condFields = null, $updateNulls = false)
 * @method  static  boolean         delete($conditions)
 * @method  static  boolean         useTransaction($yn = null)
 * @method  static  array                getSelectFields()
 * @method  static  AbstractDataMapper   setSelectFields($selectFields)
 *
 * @since  2.1.8
 */
class AbstractDataMapperProxy extends AbstractDataMapperObserver
{
	/**
	 * Property table.
	 *
	 * @var  string
	 */
	protected static $table;

	/**
	 * Property instances.
	 *
	 * @var  DataMapper[]
	 */
	protected static $instances = array();

	/**
	 * is triggered when invoking inaccessible methods in an object context.
	 *
	 * @param $name      string
	 * @param $arguments array
	 *
	 * @return mixed
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 */
	public function __call($name, $arguments)
	{
		$instance = static::getInstance();

		if (!is_callable(array($instance, $name)))
		{
			return null;
		}

		return call_user_func_array(array($instance, $name), $arguments);
	}

	/**
	 * is triggered when invoking inaccessible methods in a static context.
	 *
	 * @param $name      string
	 * @param $arguments array
	 *
	 * @return mixed
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 */
	public static function __callStatic($name, $arguments)
	{
		$instance = static::getInstance();

		if (!is_callable(array($instance, $name)))
		{
			return null;
		}

		return call_user_func_array(array($instance, $name), $arguments);
	}

	/**
	 * initialise
	 *
	 * @param AbstractDataMapper $mapper
	 *
	 * @return  void
	 */
	protected static function initialise(AbstractDataMapper $mapper)
	{
	}

	/**
	 * getInstance
	 *
	 * @param   string  $table
	 *
	 * @return  DataMapper
	 */
	public static function getInstance($table = null)
	{
		$table = $table ? : static::$table;

		if (!isset(static::$instances[$table]))
		{
			static::$instances[$table] = static::createDataMapper($table);
		}

		return static::$instances[$table];
	}

	/**
	 * createDataMapper
	 *
	 * @param   string  $table
	 *
	 * @return  DataMapper
	 */
	public static function createDataMapper($table)
	{
		$table = $table ? : static::$table;

		$mapper = new ObservableDataMapper($table);

		static::createObserver($mapper);

		static::initialise($mapper);

		return $mapper;
	}

	/**
	 * setDataMapper
	 *
	 * @param string             $table
	 * @param AbstractDataMapper $mapper
	 *
	 * @return  void
	 */
	public static function setDataMapper($table, AbstractDataMapper $mapper)
	{
		static::$instances[$table] = $mapper;
	}

	/**
	 * reset
	 *
	 * @return  void
	 */
	public static function reset()
	{
		static::$instances = array();
	}
}
