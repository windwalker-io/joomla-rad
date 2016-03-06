<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 SMS Taiwan. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

use Windwalker\Cache\RuntimeStorage;

/**
 * The RuntimeStorage class.
 *
 * @deprecated  Use Windwalker Cache package instead.
 * 
 * @since  1.0
 */
class JCacheStorageRuntime extends \JCacheStorage
{
	/**
	 * Property runtime.
	 *
	 * @var  RuntimeStorage
	 */
	protected $runtime;

	/**
	 * Constructor.
	 *
	 * @param array          $options
	 * @param RuntimeStorage $runtime
	 */
	public function __construct($options = array(), RuntimeStorage $runtime = null)
	{
		$this->runtime = $runtime ? : new RuntimeStorage;

		parent::__construct($options);
	}

	/**
	 * Get cached data by id and group
	 *
	 * @param   string   $key        The cache data id
	 * @param   string   $group      The cache data group
	 * @param   boolean  $checkTime  True to verify cache time expiration threshold
	 *
	 * @return  mixed  Boolean  false on failure or a cached data object
	 */
	public function get($key, $group, $checkTime = true)
	{
		return $this->runtime->get($group . '.' . $key);
	}

	/**
	 * Store the data to cache by id and group
	 *
	 * @param   string  $key     The cache data id
	 * @param   string  $group  The cache data group
	 * @param   string  $data   The data to store in cache
	 *
	 * @return  boolean  True on success, false otherwise
	 */
	public function store($key, $group = null, $data = null)
	{
		$this->runtime->set($group . '.' . $key, $data);

		return $this;
	}

	/**
	 * Remove an item from the cache by its unique key
	 *
	 * @param   string $key The unique cache key of the item to remove
	 * @param   string  $group  The cache data group
	 *
	 * @return  static Return self to support chaining
	 */
	public function remove($key, $group = null)
	{
		$this->runtime->remove($group . '.' . $key);

		return $this;
	}

	/**
	 * Clean cache for a group given a mode.
	 *
	 * @param   string  $group  The cache data group
	 * @param   string  $mode   The mode for cleaning cache [group|notgroup]
	 *                          group mode     : cleans all cache in the group
	 *                          notgroup mode  : cleans all cache not in the group
	 *
	 * @return  boolean  True on success, false otherwise
	 */
	public function clean($group, $mode = null)
	{
		$this->runtime->clear();

		return $this;
	}
}
