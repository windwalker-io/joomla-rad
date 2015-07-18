<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Script;

use Windwalker\DI\Container;
use Windwalker\Helper\AssetHelper;

/**
 * The ModuleContainer class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class ModuleManager
{
	/**
	 * THe asset helpers storage.
	 *
	 * @var  AssetHelper[]
	 */
	protected $assetHelpers = array();

	/**
	 * The module initialised.
	 *
	 * @var  boolean[]
	 */
	protected $initialised = array();

	/**
	 * Modules handler storage.
	 *
	 * @var  Module[]
	 */
	protected $modules = array();

	/**
	 * Property legacy.
	 *
	 * @var  boolean
	 */
	protected $legacy = false;

	/**
	 * Load RequireJS.
	 *
	 * @return  void
	 */
	public function requireJS()
	{
		if (!empty($this->initialised['requirejs']))
		{
			return;
		}

		$asset = static::getHelper();

		$asset->addJs('require.js');

		$this->initialised['requirejs'] = true;
	}

	/**
	 * Load underscore.
	 *
	 * @param boolean $noConflict Enable underscore no conflict mode.
	 *
	 * @return  void
	 */
	public function underscore($noConflict = true)
	{
		if (!empty($this->initialised['underscore']['init']))
		{
			return;
		}

		$asset = $this->getHelper();

		$asset->addJs('underscore.js');

		if ($noConflict)
		{
			$asset->internalJS(';var underscore = _.noConflict();');
		}

		$this->initialised['underscore']['init'] = true;
	}

	/**
	 * Include Backbone. Note this library may not support old IE browser.
	 *
	 * Please see: http://backbonejs.org/
	 *
	 * @param   boolean $noConflict
	 *
	 * @return  void
	 */
	public function backbone($noConflict = false)
	{
		if (!empty($this->initialised['backbone']))
		{
			return;
		}

		// Dependency
		\JHtmlJquery::framework(true);
		static::underscore();

		$asset = static::getHelper();

		$asset->addJs('backbone.js');

		if ($noConflict)
		{
			$asset->internalJS(';var backbone = Backbone.noConflict();');
		}

		$this->initialised['backbone'] = true;
	}

	/**
	 * Load Windwalker script.
	 *
	 * @return  void
	 */
	public function windwalker()
	{
		if (!empty($this->initialised['windwalker']))
		{
			return;
		}

		static::getHelper()->windwalker();

		$this->initialised['windwalker'] = true;
	}

	/**
	 * Set Module and callback.
	 *
	 * @param string   $name
	 * @param callable $handler
	 *
	 * @return  static
	 */
	public function setModule($name, $handler)
	{
		$name = strtolower($name);

		$this->modules[$name] = new Module($name, $handler, $this);

		return $this;
	}

	/**
	 * load
	 *
	 * @param string $name Module name.
	 *
	 * @return  boolean
	 */
	public function load($name)
	{
		$arguments = func_get_args();
		array_shift($arguments);

		$module = $this->getModule($name);

		if (!$module)
		{
			$app = Container::getInstance()->get('app');

			$app->enqueueMessage(sprintf('Asset module: %s not found.', strtolower($name)));

			return false;
		}

		if ($this->legacy && $module->inited())
		{
			return true;
		}

		$module->execute($this->getHelper(), $arguments);

		return true;
	}

	/**
	 * Magic method to call modules.
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return  boolean
	 */
	public function __call($name, $args = array())
	{
		if (strpos($name, 'load') === 0)
		{
			$name = substr($name, 4);
		}
		else
		{
			return false;
		}

		return $this->load(strtolower($name));
	}

	/**
	 * Get AssetHelper by option name.
	 *
	 * @param   string $option Option name.
	 *
	 * @return  AssetHelper
	 */
	public function getHelper($option = 'windwalker')
	{
		if (!empty($this->assetHelpers[$option]))
		{
			return $this->assetHelpers[$option];
		}

		$container = Container::getInstance($option);

		if ($container->exists('helper.asset'))
		{
			$asset = $container->get('helper.asset');
		}
		else
		{
			$asset = new AssetHelper($option);
		}

		return $this->assetHelpers[$option] = $asset;
	}

	/**
	 * getModule
	 *
	 * @param string $name
	 *
	 * @return  Module
	 */
	public function getModule($name)
	{
		$name = strtolower($name);

		if (empty($this->modules[$name]))
		{
			return null;
		}

		return $this->modules[$name];
	}

	/**
	 * Method to get property Modules
	 *
	 * @return  Module[]
	 */
	public function getModules()
	{
		return $this->modules;
	}

	/**
	 * Method to set property modules
	 *
	 * @param   Module[] $modules
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setModules($modules)
	{
		$this->modules = $modules;

		return $this;
	}

	/**
	 * Method to get property Legacy
	 *
	 * @return  boolean
	 */
	public function getLegacy()
	{
		return $this->legacy;
	}

	/**
	 * Method to set property legacy
	 *
	 * @param   boolean $legacy
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setLegacy($legacy)
	{
		$this->legacy = $legacy;

		return $this;
	}
}
