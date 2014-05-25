<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Image;

use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Windwalker\Helper\CurlHelper;
use Windwalker\System\ExtensionHelper;

/**
 * Thumb Object.
 *
 * @since 2.0
 */
class Thumb
{
	/**
	 * Config object.
	 *
	 * @var  Registry
	 */
	protected $config = null;

	/**
	 * Default image URL.
	 * Use some placeholder to replace variable.
	 * - {width}    => Image width.
	 * - {height}   => Image height.
	 * - {zc}       => Crop or not.
	 * - {q}        => Image quality.
	 * - {file_type}=> File type.
	 *
	 * @var string
	 */
	protected $defaultImage = 'libraries/windwalker/Resource/images/default-image.png';

	/**
	 * Extension name.
	 *
	 * @var  string
	 */
	protected $extension;

	/**
	 * Hash handler.
	 *
	 * @var  callable
	 */
	protected $hashHandler = 'md5';

	/**
	 * Constructor.
	 *
	 * @param Registry $config    The config object.
	 * @param string   $extension The extension name.
	 */
	public function __construct(Registry $config = null, $extension = null)
	{
		$config = $config ? : new Registry;
		$this->extension = $extension;

		$this->resetCachePosition();

		$this->config->merge($config);
	}

	/**
	 * Resize an image, auto catch it from remote host and generate a new thumb in cache dir.
	 *
	 * @param   string  $url       Image URL, recommend a absolute URL.
	 * @param   integer $width     Image width, do not include 'px'.
	 * @param   integer $height    Image height, do not include 'px'.
	 * @param   int     $method    Crop or not.
	 * @param   integer $q         Image quality
	 * @param   string  $file_type File type.
	 *
	 * @return  string  The cached thumb URL.
	 */
	public function resize($url = null, $width = 100, $height = 100, $method = \JImage::SCALE_INSIDE, $q = 85, $file_type = 'jpg')
	{
		if (!$url)
		{
			return $this->getDefaultImage($width, $height, $method, $q, $file_type);
		}

		$path = $this->getImagePath($url);

		try
		{
			$img = new \JImage;

			if (\JFile::exists($path))
			{
				$img->loadFile($path);
			}
			else
			{
				return $this->getDefaultImage($width, $height, $method, $q, $file_type);
			}

			// If file type not png or gif, use jpg as default.
			if ($file_type != 'png' && $file_type != 'gif')
			{
				$file_type = 'jpg';
			}

			// Using md5 hash
			$handler   = $this->hashHandler;
			$file_name = $handler($url . $width . $height . $method . $q) . '.' . $file_type;
			$file_path = $this->config['path.cache'] . '/' . $file_name;
			$file_url  = trim($this->config['url.cache'], '/') . '/' . $file_name;

			// Img exists?
			if (\JFile::exists($file_path))
			{
				return $file_url;
			}

			// Crop
			if ($method === true)
			{
				$method = \JImage::CROP_RESIZE;
			}
			elseif ($method === false)
			{
				$method = \JImage::SCALE_INSIDE;
			}

			$img = $img->generateThumbs($width . 'x' . $height, $method);

			// Save
			switch ($file_type)
			{
				case 'gif':
					$type = IMAGETYPE_GIF;
					break;
				case 'png':
					$type = IMAGETYPE_PNG;
					break;
				default :
					$type = IMAGETYPE_JPEG;
					break;
			}
			
			// Create folder
			if (!is_dir(dirname($file_path)))
			{
				\JFolder::create(dirname($file_path));
			}

			$img[0]->toFile($file_path, $type, array('quality' => $q));

			return $file_url;
		}
		catch (\Exception $e)
		{
			if (JDEBUG)
			{
				echo $e->getMessage();
			}

			return $this->getDefaultImage($width, $height, $method, $q, $file_type);
		}
	}

	/**
	 * Get the origin image path, if is a remote image, will store in temp dir first.
	 *
	 * @param   string $url  The image URL.
	 * @param   string $hash Not available now..
	 *
	 * @return  string  Image path.
	 */
	public function getImagePath($url, $hash = null)
	{
		$self = \JUri::getInstance();
		$url  = new \JUri($url);

		// Is same host?
		if ($self->getHost() == $url->getHost())
		{
			$url  = $url->toString();
			$path = str_replace(\JURI::root(), JPATH_ROOT . '/', $url);
			$path = \JPath::clean($path);
		}

		// No host
		elseif (!$url->getHost())
		{
			$url  = $url->toString();
			$path = \JPath::clean(JPATH_ROOT . '/' . $url);
		}

		// Other host
		else
		{
			$handler = $this->hashHandler;
			$path = $this->config['path.temp'] . '/' . $handler(basename($url)) . '.jpg';

			if (!is_file($path))
			{
				CurlHelper::download((string) $url, $path);
			}
		}

		return $path;
	}

	/**
	 * Crop image, will count image with height percentage, and crop from middle.
	 *
	 * @param   \JImage $img    A JImage object.
	 * @param   int     $width  Target width.
	 * @param   int     $height Target height.
	 * @param   object  $data   Image information.
	 *
	 * @return  \JImage Croped image object.
	 */
	public function crop($img, $width, $height, $data)
	{
		$ratio = $width / $height;

		$originHeight = $data->height;
		$originWidth  = $data->width;
		$originRatio  = $originWidth / $originHeight;

		$offsetX = 0;
		$offsetY = 0;

		if ($ratio > $originRatio)
		{
			$resizeWidth  = $originWidth;
			$resizeHeight = $originWidth / $ratio;

			$offsetY = ($originHeight - $resizeHeight) / 2;
		}
		else
		{
			$resizeHeight = $originHeight;
			$resizeWidth  = $originHeight * $ratio;

			$offsetX = ($originWidth - $resizeWidth) / 2;
		}

		$img = $img->crop($resizeWidth, $resizeHeight, $offsetX, $offsetY);

		return $img;
	}

	/**
	 * Set a new default image placeholder.
	 *
	 * @param   string $url Default image placeholder.
	 *
	 * @return  void
	 */
	public function setDefaultImage($url)
	{
		$this->defaultImage = $url;
	}

	/**
	 * Get default image and replace the placeholders.
	 *
	 * @param   integer $width     Image width, do not include 'px'.
	 * @param   integer $height    Image height, do not include 'px'.
	 * @param   mixed   $zc        Crop or not.
	 * @param   integer $q         Image quality
	 * @param   string  $file_type File type.
	 *
	 * @return  string  Default image.
	 */
	public function getDefaultImage($width = 100, $height = 100, $zc = 0, $q = 85, $file_type = 'jpg')
	{
		$replace['{width}']     = $width;
		$replace['{height}']    = $height;
		$replace['{zc}']        = $zc;
		$replace['{q}']         = $q;
		$replace['{file_type}'] = $file_type;

		$url = $this->defaultImage;
		$url = strtr($url, $replace);

		return $this->resize($url, $width, $height, $zc, $q, $file_type);
	}

	/**
	 * Set cache path, and all image will cache in here.
	 *
	 * @param   string $path Cache path.
	 *
	 * @return  void
	 */
	public function setCachePath($path)
	{
		$this->config['path.cache'] = $path;
	}

	/**
	 * Set cache URL, and all image will cll from here.
	 *
	 * @param   string $url Cache URL.
	 *
	 * @return  void
	 */
	public function setCacheUrl($url)
	{
		$this->config['url.cache'] = $url;
	}

	/**
	 * Set temp path, and all remote image will store in here.
	 *
	 * @param   string  $path Temp path.
	 *
	 * @return  void
	 */
	public function setTempPath($path)
	{
		$this->config['path.temp'] = $path;
	}

	/**
	 * Set cache position, will auto set cache path, url and temp path.
	 * If position set in: "cache/thumb"
	 * - Cache path:    ROOT/cache/thumb/cache
	 * - Temp path:     ROOT/cache/thumb/temp
	 * - Cache URL:     http://your-site.com/cache/thumb/cache/
	 *
	 * @param string $path The cache path.
	 *
	 * @return void
	 */
	public function setCachePosition($path)
	{
		$this->setCachePath(JPATH_ROOT . '/' . trim($path, '/') . '/cache');
		$this->setTempPath(JPATH_ROOT . '/' . trim($path, '/') . '/temp');
		$this->setCacheUrl(trim($path, '/') . '/cache');
	}

	/**
	 * Reset cache position.
	 *
	 * @return void
	 */
	public function resetCachePosition()
	{
		if ($this->extension)
		{
			$params = ExtensionHelper::getParams($this->extension);
		}
		else
		{
			$params = new Registry;
		}

		$this->config = new Registry;

		$this->config['path.cache'] = Path::clean(JPATH_ROOT . $params->get('thumb.cache-path', '/cache/thumbs/cache'));
		$this->config['path.temp']  = Path::clean(JPATH_ROOT . $params->get('thumb.temp-path',  '/cache/thumbs/temp'));
		$this->config['url.cache']  = $params->get('thumb.cache-url', '/cache/thumbs/cache');
		$this->config['url.temp']   = $params->get('thumb.temp-url',  '/cache/thumbs/cache');
	}

	/**
	 * Delete all cache and temp images.
	 *
	 * @param   boolean $temp Is delete temp dir too?
	 *
	 * @return  void
	 */
	public function clearCache($temp = false)
	{
		if (\JFolder::exists($this->config['path.cache']))
		{
			\JFolder::delete($this->config['path.cache']);
		}

		if ($temp && \JFolder::exists($this->config['path.temp']))
		{
			\JFolder::delete($this->config['path.temp']);
		}
	}

	/**
	 * Set hash handler
	 *
	 * @param   callable $hashHandler The hash handler.
	 *
	 * @return  Thumb  Return self to support chaining.
	 */
	public function setHashHandler($hashHandler)
	{
		$this->hashHandler = $hashHandler;

		return $this;
	}
}
