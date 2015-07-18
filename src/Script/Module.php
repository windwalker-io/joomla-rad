<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Script;

use Windwalker\Helper\AssetHelper;

/**
 * The Module class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class Module
{
	/**
	 * Property name.
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * Property handler.
	 *
	 * @var  callable
	 */
	protected $handler;

	/**
	 * Property manager.
	 *
	 * @var  ModuleManager
	 */
	protected $manager;

	/**
	 * Property inited.
	 *
	 * @var  boolean
	 */
	protected $inited = false;

	/**
	 * Property id.
	 *
	 * @var  string
	 */
	protected $id;

	/**
	 * Property currentArguments.
	 *
	 * @var  array
	 */
	protected $currentArguments = array();

	/**
	 * Class init.
	 *
	 * @param string        $name
	 * @param callable      $handler
	 * @param ModuleManager $manager
	 */
	public function __construct($name, $handler, ModuleManager $manager)
	{
		$this->manager = $manager;

		$this->setName($name);
		$this->setHandler($handler);
	}

	/**
	 * execute
	 *
	 * @param AssetHelper $asset
	 * @param array       $arguments
	 *
	 * @return static
	 */
	public function execute(AssetHelper $asset, $arguments = array())
	{
		$this->currentArguments = $arguments;
		$id = $this->createStateId($this->currentArguments);

		array_unshift($arguments, $asset);
		array_unshift($arguments, $this);

		call_user_func_array($this->handler, $arguments);

		$id = $this->getStateId() ? : $id;

		$this->inited['init'] = true;
		$this->inited[$id] = true;

		$this->setStateId(null);
	}

	/**
	 * Is initialized.
	 *
	 * @param string $id
	 *
	 * @return boolean
	 */
	public function inited($id = 'init')
	{
		return !empty($this->inited[$id]);
	}

	/**
	 * Is this state of parameters initialised?
	 *
	 * @param array $arguments
	 *
	 * @return bool
	 */
	public function stateInited($arguments = array())
	{
		$id = $this->getStateId() ? : $this->createStateId($arguments ? : $this->currentArguments);

		return $this->inited($id);
	}

	/**
	 * Method to get property Name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Method to set property name
	 *
	 * @param   string $name
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setName($name)
	{
		if (!is_string($name))
		{
			throw new \InvalidArgumentException('Name should be string.');
		}

		$this->name = strtolower($name);

		return $this;
	}

	/**
	 * Method to get property Handler
	 *
	 * @return  callable
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * Method to set property handler
	 *
	 * @param   callable $handler
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setHandler($handler)
	{
		if (!is_callable($handler))
		{
			throw new \InvalidArgumentException('Handler should be callable.');
		}

		$this->handler = $handler;

		return $this;
	}

	/**
	 * Method to get property Manager
	 *
	 * @return  ModuleManager
	 */
	public function getManager()
	{
		return $this->manager;
	}

	/**
	 * Method to set property manager
	 *
	 * @param   ModuleManager $manager
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setManager($manager)
	{
		$this->manager = $manager;

		return $this;
	}

	/**
	 * A toString method to make B/C.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->name;
	}

	/**
	 * getModuleID
	 *
	 * @param array  $arguments
	 *
	 * @return  string
	 */
	public function createStateId($arguments = array())
	{
		return sha1($this->name . serialize((array) $arguments));
	}

	/**
	 * Method to get property Id
	 *
	 * @return  string
	 */
	public function getStateId()
	{
		return $this->id;
	}

	/**
	 * Method to set property id
	 *
	 * @param   string $id
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setStateId($id)
	{
		$this->id = $id;

		return $this;
	}
}
