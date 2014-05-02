<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Image;

use Joomla\Registry\Registry;

/**
 * A quick thumb generator, will return generated thumb url.
 *
 * @since 1.0
 */
class ThumbHelper
{
	/**
	 * The thumb instances.
	 *
	 * @var  Thumb[]
	 */
	protected static $instance = array();

	/**
	 * Get an instance.
	 *
	 * @param string $extension
	 * @param array  $config
	 *
	 * @return  Thumb
	 */
	public static function getInstance($extension = 'lib_windwalker', $config = null)
	{
		if (empty(static::$instance[$extension]))
		{
			static::$instance[$extension] = new Thumb(new Registry($config), $extension);
		}

		return static::$instance[$extension];
	}

	/**
	 * Resize an image, auto catch it from remote host and generate a new thumb in cache dir.
	 *
	 * @param   string  $url       Image URL, recommend a absolute URL.
	 * @param   integer $width     Image width, do not include 'px'.
	 * @param   integer $height    Image height, do not include 'px'.
	 * @param   boolean $zc        Crop or not.
	 * @param   integer $q         Image quality
	 * @param   string  $file_type File type.
	 *
	 * @return  string  The cached thumb URL.
	 */
	public static function resize($url = null, $width = 100, $height = 100, $zc = false, $q = 85, $file_type = 'jpg')
	{
		return static::getInstance()->resize($url, $width, $height, $zc, $q, $file_type);
	}
}
