<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use Joomla\Uri\Uri;
use Windwalker\DI\Container;
use Windwalker\String\Utf8String;

/**
 * The Uri Helper
 *
 * @since 2.0
 */
class UriHelper
{
	/**
	 * Property isTest to determine if test mode is on or not.
	 *
	 * @var  bool
	 */
	protected static $isTest = false;

	/**
	 * Property headerBuffer for test purpose.
	 *
	 * @var  array
	 */
	public static $headerBuffer = array();

	/**
	 * Setter of property $isTest.
	 *
	 * @param   boolean $isTest
	 *
	 * @return  void
	 */
	public static function setTestMode($isTest)
	{
		self::$isTest = (bool) $isTest;
	}

	/**
	 * A base encode & decode function, will auto convert white space to plus to avoid errors.
	 *
	 * @param   string $action 'encode' OR 'decode'
	 * @param   string $url    A url or a base64 string to convert.
	 *
	 * @return  string URL or base64 decode string.
	 */
	public static function base64($action, $url)
	{
		switch ($action)
		{
			case 'encode':
				$url = base64_encode($url);
				break;

			case 'decode':
				$url = str_replace(' ', '+', $url);
				$url = base64_decode($url);
				break;
		}

		return $url;
	}

	/**
	 * A download function to hide real file path. When call this function, will start download instantly.
	 *
	 * This function should call when view has not executed yet, if header sended,
	 *  the file which downloaded will error, because download by stream will
	 *  contain header in this file.
	 *
	 * @param   string  $path     The file system path with filename & type.
	 * @param   boolean $absolute Absolute URL or not.
	 * @param   boolean $stream   Use stream or redirect to download.
	 * @param   array   $option   Some download options.
	 *
	 * @return  string
	 */
	public static function download($path, $absolute = false, $stream = false, $option = array())
	{
		$test = self::$isTest;

		static::$headerBuffer = array();

		if ($stream)
		{
			if (!$absolute)
			{
				$path = JPATH_ROOT . '/' . $path;
			}

			if (!is_file($path))
			{
				return $test ? : die;
			}

			$file = pathinfo($path);

			$filesize = filesize($path) + ArrayHelper::getValue($option, 'size_offset', 0);
			ini_set('memory_limit', ArrayHelper::getValue($option, 'memory_limit', '1540M'));

			// Set Header
			static::header('Content-Type: application/octet-stream');
			static::header('Cache-Control: no-store, no-cache, must-revalidate');
			static::header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			static::header('Content-Transfer-Encoding: binary');
			static::header('Content-Encoding: none');
			static::header('Content-type: application/force-download');
			static::header('Content-length: ' . $filesize);
			static::header('Content-Disposition: attachment; filename="' . $file['basename'] . '"');

			$handle    = fopen($path, 'rb');
			$chunksize = 1 * (1024 * 1024);

			// Start Download File by Stream
			while (!feof($handle))
			{
				$buffer = fread($handle, $chunksize);
				echo $buffer;
				$test or ob_flush();
				$test or flush();
			}

			fclose($handle);

			$test or jexit();
		}
		else
		{
			if (!$absolute)
			{
				$path = \JURI::root() . $path;
			}

			// Redirect it.
			$app = Container::getInstance()->get('app');
			$test or $app->redirect($path);

			return $path;
		}
	}

	/**
	 * header
	 *
	 * @param string $data
	 *
	 * @return  void
	 */
	protected static function header($data)
	{
		self::$isTest ? (static::$headerBuffer[] = $data) : header($data);
	}

	/**
	 * Make a URL safe.
	 * - Replace white space to '%20'.
	 *
	 * @param   string $uri The URL you want to make safe.
	 *
	 * @return  string  Replaced URL.
	 */
	public static function safe($uri)
	{
		$uri = (string) $uri;
		$uri = str_replace(' ', '%20', $uri);

		return $uri;
	}

	/**
	 * Give a relative path, return path with host.
	 *
	 * @param   string $path A system path.
	 *
	 * @return  string  Path with host added.
	 */
	public static function pathAddHost($path)
	{
		if (!$path)
		{
			return '';
		}

		// Build path
		$uri = new Uri($path);

		if ($uri->getHost())
		{
			return $path;
		}

		$uri = new Uri(\JUri::root());
		$root_path = $uri->getPath();

		if (strpos($path, $root_path) === 0)
		{
			$num  = Utf8String::strlen($root_path);
			$path = Utf8String::substr($path, $num);
		}

		$uri->setPath($uri->getPath() . $path);
		$uri->setScheme('http');
		$uri->setQuery(null);

		return $uri->toString();
	}

	/**
	 * Is home page?
	 *
	 * @return  boolean
	 */
	public static function isHome()
	{
		$uri  = \JUri::getInstance();
		$root = $uri::root(true);

		// Get site route
		$route = Utf8String::substr($uri->getPath(), Utf8String::strlen($root));

		// Remove index.php
		$route = str_replace('index.php', '', $route);

		if (! trim($route, '/') && ! $uri->getVar('option'))
		{
			return true;
		}

		return false;
	}
}
