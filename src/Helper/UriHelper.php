<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Helper;

use Windwalker\DI\Container;

/**
 * The Uri Helper
 *
 * @since 2.0
 */
class UriHelper
{
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
	 * @return  void
	 */
	public static function download($path, $absolute = false, $stream = false, $option = array())
	{
		if ($stream)
		{
			if (!$absolute)
			{
				$path = JPATH_ROOT . '/' . $path;
			}

			if (!is_file($path))
			{
				die();
			}

			$file = pathinfo($path);

			$filesize = filesize($path) + \JArrayHelper::getValue($option, 'size_offset', 0);
			ini_set('memory_limit', \JArrayHelper::getValue($option, 'memory_limit', '1540M'));

			// Set Header
			header('Content-Type: application/octet-stream');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			header('Content-Transfer-Encoding: binary');
			header('Content-Encoding: none');
			header('Content-type: application/force-download');
			header('Content-length: ' . $filesize);
			header('Content-Disposition: attachment; filename="' . $file['basename'] . '"');

			$handle    = fopen($path, 'rb');
			$chunksize = 1 * (1024 * 1024);

			// Start Download File by Stream
			while (!feof($handle))
			{
				$buffer = fread($handle, $chunksize);
				echo $buffer;
				ob_flush();
				flush();
			}

			fclose($handle);

			jexit();
		}
		else
		{
			if (!$absolute)
			{
				$path = \JURI::root() . $path;
			}

			// Redirect it.
			$app = Container::getInstance()->get('app');
			$app->redirect($path);
		}
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
		$uri = new \JURI($path);

		if ($uri->getHost())
		{
			return $path;
		}

		$uri->parse(\JURI::root());
		$root_path = $uri->getPath();

		if (strpos($path, $root_path) === 0)
		{
			$num  = \JString::strlen($root_path);
			$path = \JString::substr($path, $num);
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
		$route = \JString::substr($uri->getPath(), \JString::strlen($root));

		// Remove index.php
		$route = str_replace('index.php', '', $route);

		if (! trim($route, '/') && ! $uri->getVar('option'))
		{
			return true;
		}

		return false;
	}
}
