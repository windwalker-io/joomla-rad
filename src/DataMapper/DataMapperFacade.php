<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\DataMapper;

use Windwalker\Data\Data;
use Windwalker\Data\DataSet;

/**
 * A quick facade for DataMapper.
 *
 * @see  \Windwalker\DataMapper\DataMapper
 *
 * @method  static  DataSet  find($table, $conditions = array(), $order = null, $start = null, $limit = null)
 * @method  static  DataSet  findAll($table, $order = null, $start = null, $limit = null)
 * @method  static  Data     findOne($table, $conditions = array(), $order = null)
 * @method  static  array    findColumn($table, $column, $conditions = array(), $order = null, $start = null, $limit = null)
 * @method  static  DataSet  create($table, $dataset)
 * @method  static  Data     createOne($table, $data)
 * @method  static  DataSet  update($table, $dataset, $condFields = null, $updateNulls = false)
 * @method  static  Data     updateOne($table, $data, $condFields = null, $updateNulls = false)
 * @method  static  boolean  updateAll($table, $data, $conditions = array())
 * @method  static  DataSet  flush($table, $dataset, $conditions = array())
 * @method  static  DataSet  save($table, $dataset, $condFields = null, $updateNulls = false)
 * @method  static  Data     saveOne($table, $data, $condFields = null, $updateNulls = false)
 * @method  static  boolean  delete($table, $conditions)
 *
 * @since  2.1
 */
abstract class DataMapperFacade
{
	/**
	 * Call the DataMapper methods.
	 *
	 * @param   string  $name  Method name to call.
	 * @param   array   $args  The arguments of this method.
	 *
	 * @return  mixed  Return value of the target method.
	 */
	public static function __callStatic($name, $args)
	{
		if (empty($args[0]) || !is_string($args[0]))
		{
			throw new \InvalidArgumentException('First argument should be table name.');
		}

		$table = array_shift($args);

		$mapper = DataMapperContainer::getInstance($table);

		return call_user_func_array(array($mapper, $name), $args);
	}
}
