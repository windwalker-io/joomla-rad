<?php
/**
 * Part of joomla34b project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\DataMapper;

/**
 * The DataMapperContainer class.
 * 
 * @since  {DEPLOY_VERSION}
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
	 * getInstance
	 *
	 * @param string $table
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
	 * setInstance
	 *
	 * @param string     $table
	 * @param DataMapper $mapper
	 *
	 * @return  void
	 */
	public static function setInstance($table, DataMapper $mapper)
	{
		static::$instances[$table] = $mapper;
	}

	/**
	 * removeInstance
	 *
	 * @param string $table
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
