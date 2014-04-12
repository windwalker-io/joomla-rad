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
 * Class ControllerDelegator
 *
 * @since 1.0
 */
class ControllerDelegator
{
	/**
	 * Property class.
	 *
	 * @var  string
	 */
	public $class;

	/**
	 * Property input.
	 *
	 * @var  \JInput
	 */
	public $input;

	/**
	 * Property app.
	 *
	 * @var  \JApplicationBase
	 */
	public $app;

	/**
	 * Property config.
	 *
	 * @var  array
	 */
	public $config;

	/**
	 * Property aliases.
	 *
	 * @var  array
	 */
	protected $aliases = array();

	/**
	 * getController
	 *
	 * @param string            $class
	 * @param \JInput           $input
	 * @param \JApplicationBase $app
	 * @param array             $config
	 *
	 * @return \Windwalker\Controller\Controller
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
	 * registerAliases
	 *
	 * @return  void
	 */
	protected function registerAliases()
	{
	}

	/**
	 * addAlias
	 *
	 * @param string $class
	 * @param string $alias
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
	 * removeAlias
	 *
	 * @param string $class
	 *
	 * @return  $this
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
	 * resolveAlias
	 *
	 * @param string $class
	 *
	 * @return  mixed
	 */
	public function resolveAlias($class)
	{
		return ArrayHelper::getValue($this->aliases, $class, $class);
	}

	/**
	 * createController
	 *
	 * @param   string $class
	 *
	 * @return  \Windwalker\Controller\Controller
	 */
	protected function createController($class)
	{
		$class = $this->resolveAlias($class);

		return new $class($this->input, $this->app, $this->config);
	}
}
