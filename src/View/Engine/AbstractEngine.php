<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View\Engine;

use SplPriorityQueue;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\Container as JoomlaContainer;
use Windwalker\DI\Container;
use Joomla\Registry\Registry;

/**
 * The view engine.
 *
 * @since 2.0
 */
abstract class AbstractEngine implements EngineInterface, ContainerAwareInterface
{
	/**
	 * The paths of layouts.
	 *
	 * @var  SplPriorityQueue
	 */
	protected $paths = null;

	/**
	 * The config object.
	 *
	 * @var  Registry
	 */
	protected $config = null;

	/**
	 * Layout extension.
	 *
	 * @var  string
	 */
	protected $layoutExt = 'php';

	/**
	 * Layout name.
	 *
	 * @var  string
	 */
	protected $layout = '';

	/**
	 * Layout template.
	 *
	 * @var  string
	 */
	protected $layoutTemplate = null;

	/**
	 * Template prepared.
	 *
	 * @var  string
	 */
	protected $templatePrepared = array();

	/**
	 * The DI container.
	 *
	 * @var  Container
	 */
	protected $container = null;

	/**
	 * The data to push into template.
	 *
	 * @var  mixed
	 */
	protected $data = null;

	/**
	 * Constructor
	 *
	 * @param array                $config    The config array.
	 * @param \Joomla\DI\Container $container The DI container.
	 * @param SplPriorityQueue     $paths     The layout paths.
	 */
	public function __construct($config = array(), JoomlaContainer $container = null, SplPriorityQueue $paths = null)
	{
		// Setup dependencies.
		$this->paths  = $paths ? : new SplPriorityQueue;
		$this->config = new Registry($config);

		$this->container = $container ? : Container::getInstance();
	}

	/**
	 * Render this template.
	 *
	 * @param string $layout The layout name.
	 * @param array  $data   The data to push into layout.
	 *
	 * @return  string Rendered layout.
	 */
	public function render($layout, $data = array())
	{
		$this->layout = $layout;

		$this->data = $data;

		return $this->loadTemplate(null, $data);
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 * @param   array  $data The data to push into layout.
	 *
	 * @throws \Exception
	 * @return  string  The output of the the template script.
	 */
	public function loadTemplate($tpl = null, $data = null)
	{
		$container      = $this->container;
		$layout         = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();
		$template       = $container->get('app')->getTemplate();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;

		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl  = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the template script
		$templateFile = $this->getPath($file);

		// Change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
		{
			$alternateTmplFile = str_replace($template, $layoutTemplate, $template);

			if (is_file($alternateTmplFile))
			{
				$templateFile = $alternateTmplFile;
				$template     = $layoutTemplate;
			}
		}

		if (strpos($templateFile, \JPath::clean(JPATH_THEMES)) !== false)
		{
			$this->prepareTemplate($template);
		}

		if (!$templateFile)
		{
			throw new \Exception(\JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND',  $file . '.' . $this->layoutExt), 500);
		}

		// Unset so as not to introduce into template scope
		unset($tpl);
		unset($file);

		// Never allow a 'this' property
		if (isset($this->this))
		{
			unset($this->this);
		}

		$output = $this->execute($templateFile, $data);

		return $output;
	}

	/**
	 * Execute a template and return to loadTemplate() method.
	 *
	 * @param string $templateFile The template file name.
	 * @param array  $data         The data to push into layout.
	 *
	 * @return  mixed
	 */
	abstract protected function execute($templateFile, $data = null);

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 */
	public function escape($output)
	{
		// Escape the output.
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Prepare the template.
	 *
	 * @param string $template Template name.
	 *
	 * @return  void
	 */
	protected function prepareTemplate($template)
	{
		if ($this->templatePrepared)
		{
			return;
		}

		// Load the language file for the template
		$lang = $this->container->get('language');
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, false)
		|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, false)
		|| $lang->load('tpl_' . $template, JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", $lang->getDefault(), false, false);

		$this->templatePrepared = true;
	}

	/**
	 * Get layout template.
	 *
	 * @return  string
	 */
	public function getLayoutTemplate()
	{
		return $this->layoutTemplate;
	}

	/**
	 * Get layout paths.
	 *
	 * @param string $layout
	 *
	 * @return  string Found path.
	 */
	public function getPath($layout)
	{
		// Get the layout file name.
		$file = \JPath::clean($layout . '.' . $this->layoutExt);

		// Find the layout file path.
		$path = \JPath::find(clone($this->paths), $file);

		return $path;
	}

	/**
	 * Method to get the view paths.
	 *
	 * @return  SplPriorityQueue  The paths queue.
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   SplPriorityQueue $paths The paths queue.
	 *
	 * @return  AbstractEngine  Method supports chaining.
	 */
	public function setPaths(SplPriorityQueue $paths)
	{
		$this->paths = $paths;

		return $this;
	}

	/**
	 * Method to get layout name.
	 *
	 * @return  string The layout name.
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Method to set layout name.
	 *
	 * @param   string $layout The layout name.
	 *
	 * @return  AbstractEngine  Return self to support chaining.
	 */
	public function setLayout($layout)
	{
		if (strpos($layout, ':') === false)
		{
			$this->layout = $layout;
		}
		else
		{
			// Convert parameter to array based on :
			$temp = explode(':', $layout);
			$this->layout = $temp[1];

			// Set layout template
			$this->layoutTemplate = $temp[0];
		}

		return $this;
	}

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		if (!$this->container)
		{
			$this->container = Container::getInstance();
		}

		return $this->container;
	}

	/**
	 * Set the DI container.
	 *
	 * @param   JoomlaContainer $container The DI container.
	 *
	 * @return  AbstractEngine  Return self to support chaining.
	 */
	public function setContainer(JoomlaContainer $container)
	{
		$this->container = $container;

		return $this;
	}
}
