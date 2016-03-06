<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 SMS Taiwan. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Cache;

/**
 * The RuntimeStorage class.
 *
 * @deprecated  Use Windwalker Cache package instead.
 * 
 * @since  1.0
 */
class RuntimeStorage
{
	/**
	 * Property storage.
	 *
	 * @var  array
	 */
	protected static $store = array();

	/**
	 * Method to determine whether a storage entry has been set for a key.
	 *
	 * @param   string $key The storage entry identifier.
	 *
	 * @return  boolean
	 */
	public function exists($key)
	{
		return isset(static::$store[$key]);
	}

	/**
	 * Get cached data by id and group
	 *
	 * @param   string   $key  he cache data id
	 *
	 * @return  mixed  Boolean  false on failure or a cached data object
	 */
	public function get($key)
	{
		if (isset(static::$store[$key]))
		{
			return static::$store[$key];
		}

		return false;
	}

	/**
	 * Store the data to cache by id and group
	 *
	 * @param   string  $key    The cache data id
	 * @param   string  $data   The data to store in cache
	 *
	 * @return  boolean  True on success, false otherwise
	 */
	public function set($key, $data = null)
	{
		static::$store[$key] = $data;

		return $this;
	}

	/**
	 * Remove an item from the cache by its unique key
	 *
	 * @param string $key The unique cache key of the item to remove
	 *
	 * @return static Return self to support chaining
	 */
	public function remove($key)
	{
		if (array_key_exists($key, static::$store))
		{
			unset(static::$store[$key]);
		}

		return $this;
	}

	/**
	 * Clean cache for a group given a mode.
	 *
	 * @return  boolean  True on success, false otherwise
	 */
	public function clear()
	{
		static::$store = array();

		return $this;
	}
}
