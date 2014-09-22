<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 SMS Taiwan. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

/**
 * The JCacheControllerClosure class.
 * 
 * @since  1.0
 */
class JCacheControllerClosure extends \JCacheControllerCallback
{
	/**
	 * Call a closure to cache.
	 *
	 * @internal   mixed    $callback    Callback or string shorthand for a callback
	 * @internal   array    $args        Callback arguments
	 *
	 * @return  mixed
	 *
	 * @throws InvalidArgumentException
	 */
	public function call()
	{
		// Get callback and arguments
		$args = func_get_args();
		$key = array_shift($args);
		$callback = array_shift($args);

		if (!is_callable($callback))
		{
			throw new \InvalidArgumentException('Not a valid callable.');
		}

		$data = $this->cache->get($key);

		if ($data !== false)
		{
			return $data;
		}

		$value = call_user_func_array($callback, $args);

		$this->cache->store($value, $key, 'windwalker');

		return $this->cache->get($key, 'windwalker');
	}
}
