<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

namespace {{extension.name.cap}}\Config;

use Windwalker\Helper\PathHelper;
use Windwalker\System\Config\AbstractConfig;

defined('_JEXEC') or die;

/**
 * {{extension.name.cap}} config.
 *
 * @since 1.0
 */
abstract class Config extends AbstractConfig
{
	/**
	 * Config file type.
	 *
	 * @var  string
	 */
	protected static $type = 'json';

	/**
	 * Get config file path.
	 *
	 * @return  string
	 */
	public static function getPath()
	{
		$type = static::$type;
		$ext  = (static::$type === 'yaml') ? 'yml' : $type;

		return PathHelper::getAdmin('{{extension.element.lower}}') . '/etc/config.' . $ext;
	}
}
