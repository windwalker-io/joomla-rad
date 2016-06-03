<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Asset;

use Windwalker\DI\Container;
use Joomla\DI\Container as JoomlaContainer;
use Joomla\DI\ContainerAwareInterface;
use Windwalker\Helper\ArrayHelper;
use Windwalker\String\StringHelper;
use Windwalker\Utilities\Queue\PriorityQueue;

/**
 * The AssetManager to auto find asset files.
 *
 * @since 2.1
 */
class AssetManager implements ContainerAwareInterface
{
	/**
	 * Property instances.
	 *
	 * @var  AssetManager[]
	 */
	protected static $instances = array();

	/**
	 * Paths to scan.
	 *
	 * @var \SplPriorityQueue
	 */
	protected $paths = null;

	/**
	 * Asset cache.
	 *
	 * @var  array
	 */
	protected $cache = array();

	/**
	 * Instance name.
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * The DI container.
	 *
	 * @var Container
	 */
	protected $container = null;

	/**
	 * The JDocument instance.
	 *
	 * @var \JDocument
	 */
	protected $doc = null;

	/**
	 * The md5sum file name.
	 *
	 * @var string
	 */
	protected $sumName = 'MD5SUM';

	/**
	 * Mootools loaded.
	 *
	 * @var boolean
	 */
	protected $mootools = false;

	/**
	 * jQuery loaded.
	 *
	 * @var boolean
	 */
	protected $jquery = false;

	/**
	 * Property debug.
	 *
	 * @var  boolean
	 */
	protected $debug = false;

	/**
	 * Get AssetManager by different namespace.
	 *
	 * @param  string  $name  The instance name.
	 *
	 * @return  static
	 */
	public static function getInstance($name = 'windwalker')
	{
		if (!isset(static::$instances['windwalker']))
		{
			static::$instances['windwalker'] = new static;
		}

		$name = strtolower($name);

		if ($name == 'windwalker')
		{
			return static::$instances['windwalker'];
		}

		if (!isset(static::$instances[$name]))
		{
			static::$instances['windwalker']->resetPaths();

			$instance = clone static::$instances['windwalker'];

			$instance->setName($name);

			static::$instances[$name] = $instance;
		}

		return static::$instances[$name];
	}

	/**
	 * Constructor.
	 *
	 * @param  string                   $name   The instance name.
	 * @param  \SplPriorityQueue|array  $paths  Paths to scan assets.
	 */
	public function __construct($name = 'windwalker', $paths = null)
	{
		$this->name = $name;

		// Setup dependencies.
		$this->paths = new PriorityQueue($paths);

		if ($paths === null)
		{
			$this->registerPaths(false);
		}

		$this->debug = JDEBUG;
	}

	/**
	 * Add CSS to document.
	 *
	 * @param   string  $file   The css file name(with subfolder) to add.
	 * @param   string  $name   The instance name, also means component subfolder name,
	 *                          default is the name of this instance.
	 * @param   array  $attribs The link attributes in html element.
	 *
	 * @return AssetManager Return self to support chaining.
	 */
	public function addCSS($file, $name = null, $attribs = array())
	{
		$doc = $this->getDoc();

		if ($doc->getType() != 'html')
		{
			return $this;
		}

		// Use absolute URL
		if (strpos($file, 'http') === 0 || strpos($file, '//') === 0)
		{
			$url = $file;
			$sum = null;
		}
		// Find file from our site
		else
		{
			$filePath = $this->findFile($file, 'css', $name);

			if (!$filePath)
			{
				$this->alert(sprintf('CSS file: %s not found.', $file));

				return $this;
			}

			$sum = $filePath['sum'];

			$url = \JUri::root(true) . '/' . $filePath['file'];
		}

		$type  = ArrayHelper::getValue($attribs, 'type');
		$media = ArrayHelper::getValue($attribs, 'media');

		unset($attribs['type']);
		unset($attribs['media']);

		$doc->addStyleSheetVersion($url, $sum, $type, $media, $attribs);

		return $this;
	}

	/**
	 * Add JS file to document.
	 *
	 * @param string $file    The css file name(with subfolder) to add.
	 * @param string $name    The instance name, also means component subfolder name,
	 *                        default is the name of this instance.
	 * @param array  $attribs The link attributes in html element.
	 *
	 * @return AssetManager Return self to support chaining.
	 */
	public function addJS($file, $name = null, $attribs = array())
	{
		$doc = $this->getDoc();

		if ($doc->getType() != 'html')
		{
			return $this;
		}

		// Use absolute URL
		if (strpos($file, 'http') === 0 || strpos($file, '//') === 0)
		{
			$url = $file;
			$sum = null;
		}
		// Find file from our site
		else
		{
			$filePath = $this->findFile($file, 'js', $name);

			if (!$filePath)
			{
				$this->alert(sprintf('JS file: %s not found.', $file));

				return $this;
			}

			$sum = $filePath['sum'];

			$url = \JUri::root(true) . '/' . $filePath['file'];
		}

		$type  = ArrayHelper::getValue($attribs, 'type', 'text/javascript');
		$defer = ArrayHelper::getValue($attribs, 'defer');
		$async = ArrayHelper::getValue($attribs, 'async');

		unset($attribs['type']);
		unset($attribs['media']);

		if ($this->jquery)
		{
			\JHtml::_('jquery.framework', $this->mootools);
		}

		$doc->addScriptVersion($url, $sum, $type, $defer, $async);

		return $this;
	}

	/**
	 * Add internal CSS code.
	 *
	 * @param string $content The css code.
	 * @param string $type    Style element type.
	 *
	 * @return AssetManager Return self to support chaining.
	 */
	public function internalCSS($content, $type = 'text/css')
	{
		$this->getDoc()->addStyleDeclaration("\n" . $content . "\n", $type);

		return $this;
	}

	/**
	 * Add internal script.
	 *
	 * @param string $content The js code.
	 * @param string $type    Script element type.
	 *
	 * @return AssetManager Return self to support chaining.
	 */
	public function internalJS($content, $type = 'text/javascript')
	{
		$this->getDoc()->addScriptDeclaration(";\n" . $content . ";\n", $type);

		return $this;
	}

	/**
	 * Add Windwalker core css & js.
	 *
	 * @return void
	 */
	public function windwalker()
	{
		$app = $this->getContainer()->get('app');

		$admin = $app->isAdmin() ? '-admin' : '';

		$this->jquery();
		$this->addCSS('windwalker' . $admin . '.css');
		$this->addJS('windwalker' . $admin . '.js');
	}

	/**
	 * Method to load the jQuery JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.
	 *
	 * @param   mixed    $debug       Is debugging mode on? [optional]
	 * @param   boolean  $migrate     True to enable the jQuery Migrate plugin
	 *
	 * @return  AssetManager Return self to support chaining.
	 */
	public function jquery($debug = null, $migrate = true)
	{
		\JHtmlJquery::framework(true, $debug, $migrate);

		return $this;
	}

	/**
	 * Method to load the jQuery UI JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery UI is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  AssetManager Return self to support chaining.
	 */
	public function jqueryUI($debug = null)
	{
		\JHtmlJquery::ui(array('core'), $debug);

		return $this;
	}

	/**
	 * Method to load the MooTools & More framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of MooTools is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  AssetManager Return self to support chaining.
	 */
	public function mootools($debug = null)
	{
		\JHtmlBehavior::framework(true, $debug);

		return $this;
	}

	/**
	 * Method to load the Bootstrap JavaScript & CSS framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param  boolean  $css    Include CSS.
	 * @param  boolean  $debug  Is debugging mode on? [optional]
	 *
	 * @return  AssetManager  Return self to support chaining.
	 */
	public function bootstrap($css = false, $debug = null)
	{
		\JHtmlBootstrap::framework($debug);

		if ($css)
		{
			\JHtmlBootstrap::loadCss();
		}

		return $this;
	}

	/**
	 * Method to load the Admin isis template CSS.
	 *
	 * @param   boolean  $debug  Is debugging mode on? [optional]
	 *
	 * @return  AssetManager  Return self to support chaining.
	 */
	public function isis($debug = false)
	{
		static $loaded;

		if ($loaded)
		{
			return $this;
		}

		$doc    = $this->getDoc();
		$app    = $this->getContainer()->get('app');
		$prefix = $app->isSite() ? 'administrator/' : '';

		$this->jquery();

		$min = $debug ? '.min' : '';

		$doc->addStylesheet($prefix . 'templates/isis/css/template.css');
		$doc->addScript($prefix . 'templates/isis/js/template.js');

		$loaded = true;

		return $this;
	}

	/**
	 * Find the file we want in oaths.
	 *
	 * @param   string  $file  File name to find.
	 * @param   string  $type  File type, css or js.
	 * @param   string  $name  The instance name.
	 *
	 * @return  array|boolean  Found file & sum information.
	 */
	protected function findFile($file, $type, $name = null)
	{
		$name      = $name ? : $this->name;
		$foundpath = '';
		$sum       = '';

		$uri = new \JUri($file);

		if ($uri->getScheme())
		{
			return $file;
		}

		foreach (clone $this->paths as $path)
		{
			$path = str_replace(array('{name}', '{type}'), array($name, $type), $path);

			$path = trim($path, '/');

			// Get compressed file
			if ($foundFile = $this->getMinFile(JPATH_ROOT . '/' . $path, $file))
			{
				$file = $foundFile;
				$foundpath = $path;

				break;
			}

			$filepath = $path . '/' . $file;

			if (is_file(JPATH_ROOT . '/' . $filepath))
			{
				$foundpath = $path;

				break;
			}
		}

		if (!$foundpath)
		{
			return false;
		}

		$foundpath = str_replace(array('/', '\\'), '/', $foundpath);

		// Get SUM
		if (!$this->debug)
		{
			$sumfile = JPATH_ROOT . '/' . $foundpath . '/' . $file . '.sum';

			if (!is_file($sumfile))
			{
				$sumfile = JPATH_ROOT . '/' . $foundpath . '/' . $this->sumName;
			}

			if (is_file($sumfile))
			{
				$sum = file_get_contents($sumfile);
			}

			if ($sum)
			{
				$sum = str_replace(array("\n", "\r"), '', $sum);

				$sum = addslashes(htmlentities($sum));
			}
		}
		else
		{
			$sum = null;
		}

		// Build path
		$file = $foundpath . '/' . $file;

		return array(
			'file' => $file,
			'sum'  => $sum
		);
	}

	/**
	 * Get minify file name.
	 *
	 * - If in debug mode, we will search for .min file, if minify file not exists, retuen nomral file.
	 * - If not in debug mode, we will search for normal file, if normal file not exists but min file does, retuen min file.
	 * - If both not exists, return false.
	 *
	 * @param  string  $path  The path to check file.
	 * @param  string  $file  The file name.
	 *
	 * @return  string|false  The found file or false.
	 */
	protected function getMinFile($path, $file)
	{
		$ext = \JFile::getExt($file);

		if (StringHelper::endsWith($file, '.min.' . $ext))
		{
			$assetFile = substr($file, 0, -strlen('.min.' . $ext)) . '.' . $ext;
			$assetMinFile = $file;
		}
		else
		{
			$assetMinFile = substr($file, 0, -strlen('.' . $ext)) . '.min.' . $ext;
			$assetFile = $file;
		}

		// Use uncompressed file first
		if ($this->debug)
		{
			if (is_file($path . '/' . $assetFile))
			{
				return $assetFile;
			}

			if (is_file($path . '/' . $assetMinFile))
			{
				return $assetMinFile;
			}
		}

		// Use min file first
		else
		{
			if (is_file($path . '/' . $assetMinFile))
			{
				return $assetMinFile;
			}

			if (is_file($path . '/' . $assetFile))
			{
				return $assetFile;
			}
		}

		// All file not found, fallback to default path.
		return false;
	}

	/**
	 * Register default paths.
	 *
	 * @param boolean $includeTemplate True to include template path. Should be false before system routing.
	 *
	 * @return void
	 */
	protected function registerPaths($includeTemplate = true)
	{
		$app = $this->getContainer()->get('app');

		$prefix = $app->isAdmin() ? 'administrator/' : '';

		if ($includeTemplate)
		{
			// (1) Find: templates/[tmpl]/[type]/[name]/[file_name].[type]
			$this->paths->insert($prefix . 'templates/' . $app->getTemplate() . '/{type}/{name}', 800);

			// (2) Find: templates/[tmpl]/[type]/[file_name].[type]
			$this->paths->insert($prefix . 'templates/' . $app->getTemplate() . '/{type}', 700);
		}

		// (3) Find: components/[name]/asset/[type]/[file_name].[type]
		$this->paths->insert($prefix . 'components/{name}/asset/{type}', 600);

		// (4) Find: components/[name]/asset/[file_name].[type]
		$this->paths->insert($prefix . 'components/{name}/asset', 500);

		// (5) Find: media/[name]/[type]/[file_name].[type]
		$this->paths->insert('media/{name}/{type}', 400);

		// (6) Find: media/[name]/[file_name].[type]
		$this->paths->insert('media/{name}', 300);

		// (7) Find: media/windwalker/[type]/[file_name].[type]
		$this->paths->insert('media/windwalker/{type}', 200);

		// (8) Find: media/windwalker/[file_name].[type]
		$this->paths->insert('media/windwalker', 100);

		// (9) Find: libraries/windwalker/resource/asset/[type]/[file_name].[type] (For legacy)
		$this->paths->insert('libraries/windwalker/resource/asset/{type}', 50);

		// (10) Find: libraries/windwalker/resource/assets/[file_name].[type] (For legacy)
		$this->paths->insert('libraries/windwalker/resource/asset', 20);

		// (11) Find: libraries/windwalker/assets/[file_name].[type] (For legacy)
		$this->paths->insert('libraries/windwalker/assets', 10);
	}

	/**
	 * Reset paths.
	 *
	 * @param boolean $includeTemplate True to include template path. Should be false before system routing.
	 *
	 * @return  AssetManager
	 */
	public function resetPaths($includeTemplate = true)
	{
		$this->setPaths(new \SplPriorityQueue)->registerPaths($includeTemplate);

		return $this;
	}

	/**
	 * Get the DI container.
	 *
	 * @param   string  $name  The name of container.
	 *
	 * @return  Container
	 */
	public function getContainer($name = null)
	{
		if (!($this->container instanceof JoomlaContainer))
		{
			$name = ($name == 'windwalker') ? null : $name;

			$this->container = Container::getInstance($name);
		}

		return $this->container;
	}

	/**
	 * Set the DI container.
	 *
	 * @param  JoomlaContainer $container The DI container.
	 *
	 * @return AssetManager Return self to support chaining.
	 */
	public function setContainer(JoomlaContainer $container)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * Get instance name.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set instance name.
	 *
	 * @param string $name Instance name.
	 *
	 * @return AssetManager Return self to support chaining.
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get scan paths.
	 *
	 * @return  PriorityQueue  The paths object.
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * Set scan paths.
	 *
	 * @param \SplPriorityQueue $paths The paths.
	 *
	 * @return  AssetManager Return self to support chaining.
	 */
	public function setPaths(\SplPriorityQueue $paths)
	{
		$this->paths = new PriorityQueue($paths);

		return $this;
	}

	/**
	 * Get JDocument.
	 *
	 * @return  \JDocument The JDocument object.
	 */
	public function getDoc()
	{
		if (!($this->doc instanceof \JDocument))
		{
			$this->doc = $this->getContainer()->get('document');
		}

		return $this->doc;
	}

	/**
	 * Set JDocument
	 *
	 * @param \JDocument $doc The JDocument object.
	 *
	 * @return  AssetManager Return self to support chaining.
	 */
	public function setDoc(\JDocument $doc)
	{
		$this->doc = $doc;

		return $this;
	}

	/**
	 * Alert if error occurred.
	 *
	 * @param string $msg  Message content.
	 * @param string $type Message type.
	 *
	 * @return AssetManager Return self to support chaining.
	 */
	protected function alert($msg, $type = 'warning')
	{
		if ($this->debug)
		{
			$this->getContainer()->get('app')->enqueueMessage($msg, $type);
		}

		return $this;
	}

	/**
	 * Set sum name.
	 *
	 * @param string $sumName The sum name.
	 *
	 * @return  AssetManager Return self to support chaining.
	 */
	public function setSumName($sumName)
	{
		$this->sumName = $sumName;

		return $this;
	}

	/**
	 * Clone this object.
	 *
	 * @return  AssetManager Return cloned object.
	 */
	public function __clone()
	{
		$this->paths = clone $this->paths;
	}

	/**
	 * Method to get property Debug.
	 *
	 * @param  boolean  $bool  Support debug mode to random version.
	 *
	 * @return  boolean|static  Return debug value or self to chaining.
	 */
	public function isDebug($bool = null)
	{
		if ($bool !== null)
		{
			$this->debug = (bool) $bool;

			return $this;
		}

		return $this->debug;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array.
	 *
	 * You can add \\ before a function string that this string will keep as a real JS function.
	 *
	 * @param   mixed  $data      The data to convert to JS object.
	 * @param   bool   $quoteKey  Quote key by double quote or not.
	 *                            - TRUE:  {"key":"value"}
	 *                            - FALSE: {key:"value"}
	 *
	 * @return  string  JavaScript object notation representation of the array
	 */
	public static function getJSObject($data, $quoteKey = true)
	{
		if ($data === null)
		{
			return 'null';
		};

		$output = '';

		switch (gettype($data))
		{
			case 'boolean':
				$output .= $data ? 'true' : 'false';
				break;

			case 'float':
			case 'double':
			case 'integer':
				$output .= $data + 0;
				break;

			case 'array':
				if (!ArrayHelper::isAssociative($data))
				{
					$child = array();

					foreach ($data as $value)
					{
						$child[] = static::getJSObject($value, $quoteKey);
					}

					$output .= '[' . implode(',', $child) . ']';
					break;
				}

			case 'object':
				$array = is_object($data) ? get_object_vars($data) : $data;

				$row = array();

				foreach ($array as $key => $value)
				{
					$key = json_encode($key);

					if (!$quoteKey)
					{
						$key = substr(substr($key, 0, -1), 1);
					}

					$row[] = $key . ':' . static::getJSObject($value, $quoteKey);
				}

				$output .= '{' . implode(',', $row) . '}';
				break;

			default:  // anything else is treated as a string
				return strpos($data, '\\') === 0 ? substr($data, 1) : json_encode($data);
				break;
		}

		return $output;
	}
}
