<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 SMS Taiwan. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Cache;

/**
 * The RuntimeCache class.
 * 
 * @since  1.0
 */
class RuntimeCache
{
	/**
	 * Get cache handler.
	 *
	 * Note this cache will ignore the cache setting in Joomla configuration.
	 *
	 * @param   string  $handler  The cache handler name.
	 * @param   string  $storage  The storage name.
	 *
	 * @return  \JCache|\JCacheController|\JCacheControllerClosure
	 */
	public static function getCache($handler = 'closure', $storage = 'runtime')
	{
		static $included = false;

		if (!$included)
		{
			\JCacheStorage::addIncludePath(__DIR__);
			\JCacheController::addIncludePath(__DIR__);

			$included = true;
		}

		$handler = $handler ? : 'closure';

		$cache = \JFactory::getCache('windwalker', $handler, $storage);

		$cache->setCaching(true);

		return $cache;
	}
}
