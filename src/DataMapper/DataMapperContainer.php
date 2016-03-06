<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\DataMapper;

/**
 * The DataMapperContainer to store DataMapper singleton.
 * 
 * @since  2.1
 */
abstract class DataMapperContainer
{
	/**
	 * Property instances.
	 *
	 * @var  DataMapper[]
	 */
	protected static $instances = array();

	/**
	 * Get a DataMapper instance and cache it.
	 *
	 * @param  string  $table  The table name of this DataMapper.
	 *
	 * @return  DataMapper
	 */
	public static function getInstance($table)
	{
		if (empty(static::$instances[$table]))
		{
			static::$instances[$table] = new DataMapper($table);
		}

		return static::$instances[$table];
	}

	/**
	 * Set a DataMapper instance into container.
	 *
	 * @param   string      $table   The table name of this DataMapper.
	 * @param   DataMapper  $mapper  The DataMapper instance to set.
	 *
	 * @return  void
	 */
	public static function setInstance($table, DataMapper $mapper)
	{
		static::$instances[$table] = $mapper;
	}

	/**
	 * Remove a DataMapper from container.
	 *
	 * @param   string  $table  The DataMapper name to remove.
	 *
	 * @return  void
	 */
	public static function removeInstance($table)
	{
		if (!empty(static::$instances[$table]))
		{
			unset(static::$instances[$table]);
		}
	}
}
