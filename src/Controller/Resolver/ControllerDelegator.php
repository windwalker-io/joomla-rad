<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Controller\Resolver;

use Joomla\Utilities\ArrayHelper;
use Windwalker\String\StringNormalise;

/**
 * The Controller Delegator.
 *
 * @since 2.0
 */
class ControllerDelegator
{
	/**
	 * Controller class.
	 *
	 * @var  string
	 */
	public $class = null;

	/**
	 * Input object.
	 *
	 * @var  \JInput
	 */
	public $input = null;

	/**
	 * Application object.
	 *
	 * @var  \JApplicationBase
	 */
	public $app = null;

	/**
	 * The controller config.
	 *
	 * @var  array
	 */
	public $config = null;

	/**
	 * Alias to get controller.
	 *
	 * @var  array
	 */
	protected $aliases = array();

	/**
	 * Method to get controller.
	 *
	 * @param string            $class  Controller class.
	 * @param \JInput           $input  The input object.
	 * @param \JApplicationBase $app    The application object.
	 * @param array             $config The controller config.
	 *
	 * @return \Windwalker\Controller\Controller Controller instance.
	 */
	public function getController($class, \JInput $input, \JApplicationBase $app, $config = array())
	{
		$this->class  = $class;
		$this->input  = $input;
		$this->app    = $app;
		$this->config = $config;

		$this->registerAliases();

		return $this->createController($class);
	}

	/**
	 * Register aliases.
	 *
	 * @return  void
	 */
	protected function registerAliases()
	{
	}

	/**
	 * Add alias.
	 *
	 * @param string $class  Class name.
	 * @param string $alias  Alias for this controller.
	 *
	 * @return  $this
	 */
	public function addAlias($class, $alias)
	{
		$class = StringNormalise::toClassNamespace($class);
		$alias = StringNormalise::toClassNamespace($alias);

		$this->aliases[$class] = $alias;

		return $this;
	}

	/**
	 * Remove alias.
	 *
	 * @param   string $class The class to remove.
	 *
	 * @return  ControllerDelegator Return self to support chaining.
	 */
	public function removeAlias($class)
	{
		$class = StringNormalise::toClassNamespace($class);

		if (!empty($this->aliases[$class]))
		{
			unset($this->aliases[$class]);
		}

		return $this;
	}

	/**
	 * Resolve alias.
	 *
	 * @param   string $class Controller class.
	 *
	 * @return  string Alias name.
	 */
	public function resolveAlias($class)
	{
		return ArrayHelper::getValue($this->aliases, $class, $class);
	}

	/**
	 * Create Controller.
	 *
	 * @param   string $class Controller class name.
	 *
	 * @return  \Windwalker\Controller\Controller Controller instance.
	 */
	protected function createController($class)
	{
		$class = $this->resolveAlias($class);

		return new $class($this->input, $this->app, $this->config);
	}
}
