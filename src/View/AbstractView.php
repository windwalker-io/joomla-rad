<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\View;

use Joomla\DI\Container as JoomlaContainer;
use Joomla\DI\ContainerAwareInterface;
use Windwalker\Data\Data;
use Windwalker\DI\Container;
use Windwalker\Model\Model;

/**
 * The basic abstract view.
 *
 * @since 2.0
 */
abstract class AbstractView implements \JView, ContainerAwareInterface
{
	/**
	 * The model object.
	 *
	 * @var array
	 */
	protected $model = array();

	/**
	 * The default model.
	 *
	 * @var string
	 */
	protected $defaultModel = null;

	/**
	 * The data object.
	 *
	 * @var \Windwalker\Data\Data
	 */
	protected $data = null;

	/**
	 * The DI container.
	 *
	 * @var Container
	 */
	protected $container = null;

	/**
	 * The component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = null;

	/**
	 * The text prefix for translate.
	 *
	 * @var  string
	 */
	protected $textPrefix = null;

	/**
	 * The component option name.
	 *
	 * @var string
	 */
	protected $option = null;

	/**
	 * The view name.
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Method to instantiate the view.
	 *
	 * @param Model      $model     The model object.
	 * @param Container  $container DI Container.
	 * @param array      $config    View config.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array())
	{
		// Setup dependencies.
		if ($model)
		{
			$modelName = $model->getName();

			$this->defaultModel = strtolower($modelName);

			$this->model[strtolower($modelName)] = $model;
		}

		// Prepare data
		$this->data = $this->data ? : \JArrayHelper::getValue($config, 'data', new Data);

		// Prepare prefix
		$this->prefix = $this->prefix ? : \JArrayHelper::getValue($config, 'prefix', $this->getPrefix());

		// Prepare option
		$this->option = $this->option ? : \JArrayHelper::getValue($config, 'option', 'com_' . $this->prefix);

		// Prepare name
		$this->name = $this->name ? : \JArrayHelper::getValue($config, 'name', $this->getName());

		// Prepare textPrefix
		$this->textPrefix = $this->textPrefix ? : \JArrayHelper::getValue($config, 'text_prefix', $this->option);

		$this->textPrefix = strtoupper($this->textPrefix);

		$this->container = $container ? : Container::getInstance($this->option);
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 */
	public function escape($output)
	{
		return $output;
	}

	/**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @throws  \RuntimeException
	 */
	public function render()
	{
		$this->prepareRender();

		$this->prepareData();

		$output = $this->doRender();

		return $this->postRender($output);
	}

	/**
	 * Do render action.
	 *
	 * @return  string Rendered string.
	 *
	 * @throws \RuntimeException
	 */
	abstract protected function doRender();

	/**
	 * Prepare render hook.
	 *
	 * @return  void
	 */
	protected function prepareRender()
	{
	}

	/**
	 * Prepare data hook.
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
	}

	/**
	 * Post render hook.
	 *
	 * @param string $output The output string.
	 *
	 * @return  string The output string.
	 */
	protected function postRender($output)
	{
		return $output;
	}

	/**
	 * Method to get the model object
	 *
	 * @param   string  $name  The name of the model (optional)
	 *
	 * @return  \JModel|null  Windwalker model object
	 */
	public function getModel($name = null)
	{
		if (!$name)
		{
			$name = $this->defaultModel;
		}

		$name = strtolower($name);

		if (empty($this->model[$name]))
		{
			return null;
		}

		return $this->model[$name];
	}

	/**
	 * Method to add a model to the view.  We support a multiple model single
	 * view system by which models are referenced by classname.
	 *
	 * @param   Model   $model    The model to add to the view.
	 * @param   boolean $default  Is this the default model?
	 *
	 * @return  object  The added model.
	 */
	public function setModel(Model $model, $default = false)
	{
		$name = strtolower($model->getName());
		$this->model[$name] = $model;

		if ($default)
		{
			$this->defaultModel = $name;
		}

		return $model;
	}

	/**
	 * Method to get data from a registered model or a property of the view.
	 *
	 * @param   string  $cmd    The name of the method to call on the model or the property to get
	 * @param   string  $model  The name of the model to reference or the default value [optional]
	 * @param   array   $args   The arguments to send to model methods.
	 *
	 * @return  mixed  The return value of the method
	 */
	public function get($cmd, $model = null, $args = array())
	{
		// If $model is null we use the default model
		$model = $this->getModel($model);

		// First check to make sure the model requested exists
		if (!$model)
		{
			return null;
		}

		// Model exists, let's build the method name
		$method = 'get' . ucfirst($cmd);

		// Does the method exist?
		if (!method_exists($model, $method))
		{
			// $method = 'load' . ucfirst($cmd);

			return null;
		}

		// The method exists, let's call it and return what we get
		$result = call_user_func_array(array($model, $method), $args);

		return $result;
	}

	/**
	 * Get data object from cache.
	 *
	 * @return Data The data object.
	 */
	public function getData()
	{
		if (!$this->data)
		{
			$this->data = new Data;
		}

		return $this->data;
	}

	/**
	 * Set data object.
	 *
	 * @param Data $data The data object.
	 *
	 * @return AbstractView Return self to support chaining.
	 */
	public function setData($data)
	{
		$this->data = $data;

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
			$this->container = Container::getInstance($this->option);
		}

		return $this->container;
	}

	/**
	 * Set the DI container.
	 *
	 * @param   JoomlaContainer $container The DI container.
	 *
	 * @return  AbstractView Return self to support chaining.
	 */
	public function setContainer(JoomlaContainer $container)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * Get component option name.
	 *
	 * @return  string The component option name.
	 */
	public function getOption()
	{
		return $this->option;
	}

	/**
	 * Set option name.
	 *
	 * @param string $option The component option name.
	 *
	 * @return  AbstractView Return self to support chaining.
	 */
	public function setOption($option)
	{
		$this->option = $option;

		return $this;
	}

	/**
	 * Get prefix.
	 *
	 * @return  string The component prefix.
	 *
	 * @throws \Exception
	 */
	public function getPrefix()
	{
		if (!$this->prefix)
		{
			$r = null;

			if (!preg_match('/(.*)View/i', get_class($this), $r))
			{
				throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}

			$this->prefix = strtolower($r[1]);
		}

		return $this->prefix;
	}

	/**
	 * Ser prefix.
	 *
	 * @param   string $prefix The component prefix.
	 *
	 * @return  AbstractView  Return self to support chaining.
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;

		return $this;
	}

	/**
	 * Method to get the view name
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 *
	 * @throws  \Exception
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$classname = get_class($this);
			$viewpos = strpos($classname, 'View');

			if ($viewpos === false)
			{
				throw new \Exception(\JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}

			$lastPart  = substr($classname, $viewpos + 4);
			$pathParts = explode(' ', \JStringNormalise::fromCamelCase($lastPart));

			if (!empty($pathParts[1]))
			{
				$this->name = strtolower($pathParts[0]);
			}
			else
			{
				$this->name = strtolower($lastPart);
			}
		}

		return $this->name;
	}

	/**
	 * set view name.
	 *
	 * @param   string $name The view name.
	 *
	 * @return  AbstractView  Return self to support chaining.
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}
}
