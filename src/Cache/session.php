<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 SMS Taiwan. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

/**
 * The JCacheStorageSession class.
 *
 * @deprecated  Use Windwalker Cache package instead.
 * 
 * @since  1.0
 */
class JCacheStorageSession extends JCacheStorage
{
	/**
	 * Property session.
	 *
	 * @var  JSession
	 */
	protected $session;
	
	/**
	 * Constructor.
	 *
	 * @param array    $options
	 * @param JSession $session
	 */
	public function __construct($options = array(), JSession $session = null)
	{
		$this->session = $session ? : JFactory::getSession();

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
		$value = $this->session->get('cache.' . $group . '.' . $key);

		if (!$value)
		{
			return false;
		}

		return $value;
	}

	/**
	 * Store the data to cache by id and group
	 *
	 * @param   string  $key    The cache data id
	 * @param   string  $group  The cache data group
	 * @param   string  $data   The data to store in cache
	 *
	 * @return  boolean  True on success, false otherwise
	 */
	public function store($key, $group = null, $data = null)
	{
		$data = json_decode(json_encode($data));

		$this->session->set('cache.' . $group . '.' . $key, $data);

		return $this;
	}

	/**
	 * Remove an item from the cache by its unique key
	 *
	 * @param string $key The unique cache key of the item to remove
	 * @param   string  $group  The cache data group
	 *
	 * @return static Return self to support chaining
	 */
	public function remove($key, $group = null)
	{
		$this->session->clear('cache.' . $group . '.' . $key);

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
	 *
	 * @since   11.1
	 */
	public function clean($group, $mode = null)
	{
		return $this;
	}
}
