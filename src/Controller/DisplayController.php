<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Controller;

use Windwalker\Model\Model;
use Windwalker\Utilities\Queue\PriorityQueue;
use Windwalker\View\AbstractView;
use Windwalker\View\Html\AbstractHtmlView;

defined('_JEXEC') or die('Restricted access');

/**
 * Base Display Controller
 *
 * @since 2.0
 */
class DisplayController extends Controller
{
	/**
	 * Default View name.
	 *
	 * @var string
	 */
	protected $defaultView;

	/**
	 * If true, the view output will be cached.
	 *
	 * @var boolean
	 */
	protected $cachable = false;

	/**
	 * An array of safe url parameters and their variable types, for valid values.
	 *
	 * @see \JFilterInput::clean()
	 *
	 * @var array
	 */
	protected $urlParams = array();

	/**
	 * The view cache.
	 *
	 * @var  \Windwalker\View\AbstractView
	 */
	protected $view = null;

	/**
	 * The page type format.
	 *
	 * @var  string
	 */
	protected $format = 'html';

	/**
	 * Property subModels.
	 *
	 * @var  Model[]
	 */
	protected $subModels = array();

	/**
	 * Prepare execute hook.
	 *
	 * @throws \LogicException
	 * @return void
	 */
	protected function prepareExecute()
	{
		// Get some data.
		$document   = $this->container->get('document');
		$viewName   = $this->input->get('view', $this->defaultView);
		$viewFormat = $this->format = $document->getType();
		$layoutName = $this->input->getString('layout', 'default');

		// Get View and register Model to it.
		$view = $this->getView($viewName, $viewFormat);

		if (!$view)
		{
			throw new \LogicException(sprintf('View not found. Name: %s, Format: %s', $viewName, $viewFormat));
		}

		$this->assignModel($view);

		// Set template layout to view.
		if ($view instanceof AbstractHtmlView)
		{
			$view->setLayout($layoutName);
		}

		// Push JDocument to View
		$view->getData()->set('document', $document);

		$this->view = $view;

		// Redirect to GET
		if (strtoupper($this->input->getMethod()) == 'POST')
		{
			$this->redirect(\JUri::getInstance());

			return;
		}
	}

	/**
	 * Method to run this controller.
	 *
	 * @return  mixed  A rendered view or true
	 */
	protected function doExecute()
	{
		// Display the view
		$config = $this->container->get('joomla.config');

		if ($this->cachable && $this->format != 'feed' && $config->get('caching') >= 1)
		{
			$option = $this->input->get('option');
			$cache = \JFactory::getCache($option, 'view');

			// Register url params for JCache.
			if (is_array($this->urlParams))
			{
				if (!empty($this->app->registeredurlparams))
				{
					$registeredurlparams = $this->app->registeredurlparams;
				}
				else
				{
					$registeredurlparams = new \stdClass;
				}

				foreach ($this->urlParams as $key => $value)
				{
					// Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}

				$this->app->registeredurlparams = $registeredurlparams;
			}

			return $cache->get($this->view, 'render');
		}

		// Set sub models
		foreach ($this->subModels as $subModel)
		{
			$this->view->setModel($subModel);
		}

		return  $this->view->render();
	}

	/**
	 * Cachable getter.
	 *
	 * @return boolean
	 */
	public function getCachable()
	{
		return $this->cachable;
	}

	/**
	 * Cachable setter.
	 *
	 * @param boolean $cachable Is Cachable.
	 *
	 * @return $this
	 */
	public function setCachable($cachable)
	{
		$this->cachable = $cachable;

		return $this;
	}

	/**
	 * UrlParams getter.
	 *
	 * @return array
	 */
	public function getUrlParams()
	{
		return $this->urlParams;
	}

	/**
	 * UrlParams setter.
	 *
	 * @param array $urlParams The urlParams property.
	 *
	 * @return $this
	 */
	public function setUrlParams($urlParams)
	{
		$this->urlParams = $urlParams;

		return $this;
	}

	/**
	 * Method to get a reference to the current view and load it if necessary.
	 *
	 * @param   string   $name     The view name. Optional, defaults to the controller name.
	 * @param   string   $type     The view type. Optional.
	 * @param   array    $config   Configuration array for view. Optional.
	 * @param   boolean  $forceNew Force new instance.
	 *
	 * @return  \Windwalker\View\AbstractView  Reference to the view or an error.
	 */
	public function getView($name = null, $type = 'html', $config = array(), $forceNew = false)
	{
		// Get the name.
		if (!$name)
		{
			$name = $this->getName();
		}

		$container = $this->getContainer();
		$viewKey   = 'view.' . strtolower($name) . '.' . strtolower($type);

		if (!$container->exists($viewKey) || $forceNew)
		{
			// Get View
			$type     = ucfirst($type);
			$prefix   = ucfirst($this->getPrefix()) . 'View';
			$viewName = $prefix . ucfirst($name) . $type;

			if (!class_exists($viewName))
			{
				$viewName = 'Windwalker\View\\' . $type . '\\' . $type . 'View';
			}

			// Load view
			if (!class_exists($viewName))
			{
				throw new \LogicException('View: ' . $name . ' with type: ' . $type . ' not found.');
			}

			$model  = $this->getModel($name);
			$paths  = $this->getTemplatePath($name);

			$defaultConfig = array(
				'name'   => strtolower($name),
				'option' => strtolower($this->option),
				'prefix' => strtolower($this->getPrefix())
			);

			$config = array_merge($defaultConfig, $config);

			$view = new $viewName($model, $container, $config, $paths);

			$container->share($viewKey, $view);
		}

		return $container->get($viewKey);
	}

	/**
	 * Get template path.
	 *
	 * @param string  $view  The view name.
	 *
	 * @return PriorityQueue The queue object.
	 */
	public function getTemplatePath($view)
	{
		// Register the layout paths for the view
		$componentFolder = $this->getComponentPath();
		$paths = new PriorityQueue;

		$view = $view ? : $this->defaultView;

		// Theme override path.
		$paths->insert(JPATH_THEMES . '/' . $this->app->getTemplate() . '/html/' . $this->option . '/' . $view, 200);

		// View tmpl path.
		$paths->insert($componentFolder . '/view/' . $view . '/tmpl', 100);

		return $paths;
	}

	/**
	 * Assign Models Hook.
	 *
	 * @param AbstractView $view The view object.
	 *
	 * @return void
	 */
	protected function assignModel($view)
	{
	}

	/**
	 * Get default view name.
	 *
	 * @return string
	 */
	public function getDefaultView()
	{
		if (!$this->defaultView)
		{
			$this->defaultView = $this->getName();
		}

		return $this->defaultView;
	}
}
