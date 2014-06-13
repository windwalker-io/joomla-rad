<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Helper;

use Windwalker\DI\Container;
use Joomla\DI\Container as JoomlaContainer;
use Joomla\DI\ContainerAwareInterface;

/**
 * The Asset Helper
 *
 * @since 2.0
 */
class AssetHelper implements ContainerAwareInterface
{
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
	protected $sumName = 'md5sum';

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
	 * Constructor.
	 *
	 * @param string             $name  The instance name.
	 * @param \SplPriorityQueue  $paths Paths to scan assets.
	 */
	public function __construct($name = 'windwalker', $paths = null)
	{
		$this->name = $name;

		// Setup dependencies.
		$this->paths = $paths ? : new \SplPriorityQueue((array) $paths);

		$this->registerPaths(false);
	}

	/**
	 * Add CSS to document.
	 *
	 * @param string $file    The css file name(with subfolder) to add.
	 * @param string $name    The instance name, also means component subfolder name,
	 *                        default is the name of this instance.
	 * @param array  $attribs The link attributes in html element.
	 *
	 * @return AssetHelper Return self to support chaining.
	 */
	public function addCSS($file, $name = null, $attribs = array())
	{
		$doc = $this->getDoc();

		if ($doc->getType() != 'html')
		{
			return $this;
		}

		$filePath = $this->findFile($file, 'css', $name);

		if (!$filePath)
		{
			$this->alert(sprintf('CSS file: %s not found.', $file));

			return $this;
		}

		$type  = \JArrayHelper::getValue($attribs, 'type');
		$media = \JArrayHelper::getValue($attribs, 'media');

		unset($attribs['type']);
		unset($attribs['media']);

		$doc->addStyleSheetVersion(\JUri::root(true) . '/' . $filePath['file'], $filePath['sum'], $type, $media, $attribs);

		return $this;
	}

	/**
	 * Add JS file to document.
	 *
	 * @param string $file    The css file name(with subfolder) to add.
	 * @param string $name    The instance name, also means component subfolder name,
	 *                        default is the name of this instance.
	 * @param string $version The version of this asset(not used now).
	 * @param array  $attribs The link attributes in html element.
	 *
	 * @return AssetHelper Return self to support chaining.
	 */
	public function addJS($file, $name = null, $version = null, $attribs = array())
	{
		$doc = $this->getDoc();

		if ($doc->getType() != 'html')
		{
			return $this;
		}

		$filePath = $this->findFile($file, 'js', $name);

		if (!$filePath)
		{
			$this->alert(sprintf('JS file: %s not found.', $file));

			return $this;
		}

		$type  = \JArrayHelper::getValue($attribs, 'type');
		$defer = \JArrayHelper::getValue($attribs, 'defer');
		$async = \JArrayHelper::getValue($attribs, 'async');

		unset($attribs['type']);
		unset($attribs['media']);

		if ($this->mootools)
		{
			\JHtml::_('behavior.framework');
		}

		if ($this->jquery)
		{
			\JHtml::_('jquery.framework', $this->mootools);
		}

		$doc->addScriptVersion(\JUri::root(true) . '/' . $filePath['file'], $filePath['sum'], $type, $defer, $async);

		return $this;
	}

	/**
	 * Add internal CSS code.
	 *
	 * @param string $content The css code.
	 * @param string $type    Style element type.
	 *
	 * @return AssetHelper Return self to support chaining.
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
	 * @return AssetHelper Return self to support chaining.
	 */
	public function internalJS($content, $type = 'text/javascript')
	{
		$this->getDoc()->addScriptDeclaration("\n" . $content . "\n", $type);

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
	 * @return  AssetHelper Return self to support chaining.
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
	 * @return  AssetHelper Return self to support chaining.
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
	 * @return  AssetHelper Return self to support chaining.
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
	 * @param bool    $css   Include CSS.
	 * @param boolean $debug Is debugging mode on? [optional]
	 *
	 * @return AssetHelper Return self to support chaining.
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
	 * @param boolean $debug Is debugging mode on? [optional]
	 *
	 * @return AssetHelper Return self to support chaining.
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
	 * @param string $file  File name to find.
	 * @param string $type  File type, css or js.
	 * @param string $name  The instance name.
	 *
	 * @return array|boolean Found file & sum information.
	 */
	protected function findFile($file, $type, $name = null)
	{
		$name      = $name ? : $this->name;
		$foundpath = '';
		$sum       = '';

		foreach (clone $this->paths as $path)
		{
			$path = str_replace(array('{name}', '{type}'), array($name, $type), $path);

			$path = trim($path, '/');

			// Get compressed file
			if (!JDEBUG && is_file(JPATH_ROOT . '/' . $path . '/' . ($minname = $this->getMinName($file))))
			{
				$foundpath = $path;
				$file      = trim($minname, '/');

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
		if (!JDEBUG)
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
	 * Extract file name and add min after name.
	 *
	 * @param string $file The file name.
	 *
	 * @return string
	 */
	public function getMinName($file)
	{
		$file = new \SplFileInfo($file);
		$ext  = $file->getExtension();
		$name = $file->getBasename('.' . $ext);

		$name = $name . '.min.' . $ext;

		return $file->getPath() . '/' . $name;
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

		if ($includeTemplate && $app->isSite())
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

		// (9) Find: libraries/windwalker/Resource/asset/[type]/[file_name].[type] (For legacy)
		$this->paths->insert('libraries/windwalker/Resource/asset/{type}', 50);

		// (10) Find: libraries/windwalker/assets/[file_name].[type] (For legacy)
		$this->paths->insert('libraries/windwalker/Resource/asset', 20);

		// (11) Find: libraries/windwalker/assets/[file_name].[type] (For legacy)
		$this->paths->insert('libraries/windwalker/assets', 10);
	}

	/**
	 * Reset paths.
	 *
	 * @param boolean $includeTemplate True to include template path. Should be false before system routing.
	 *
	 * @return  AssetHelper
	 */
	public function resetPaths($includeTemplate = true)
	{
		$this->setPaths(new \SplPriorityQueue)->registerPaths($includeTemplate);

		return $this;
	}

	/**
	 * Get the DI container.
	 *
	 * @param   string $name
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
	 * @return AssetHelper Return self to support chaining.
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
	 * @return AssetHelper Return self to support chaining.
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get scan paths.
	 *
	 * @return  \SplPriorityQueue
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
	 * @return  AssetHelper Return self to support chaining.
	 */
	public function setPaths(\SplPriorityQueue $paths)
	{
		$this->paths = $paths;

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
	 * @return  AssetHelper Return self to support chaining.
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
	 * @return AssetHelper Return self to support chaining.
	 */
	protected function alert($msg, $type = 'warning')
	{
		if (JDEBUG)
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
	 * @return  AssetHelper Return self to support chaining.
	 */
	public function setSumName($sumName)
	{
		$this->sumName = $sumName;

		return $this;
	}

	/**
	 * Clone this object.
	 *
	 * @return  AssetHelper Return cloned object.
	 */
	public function __clone()
	{
		$this->paths = clone $this->paths;
	}
}
