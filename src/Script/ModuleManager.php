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
	 * Class init.
	 */
	public function __construct()
	{
		$this->registerCoreModules();
	}

	/**
	 * Load RequireJS.
	 *
	 * @return  void
	 */
	public function requireJS()
	{
		$this->load(__FUNCTION__);
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
		$this->load(__FUNCTION__, $noConflict);
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
		$this->load(__FUNCTION__, $noConflict);
	}

	/**
	 * Load Windwalker script.
	 *
	 * @return  void
	 */
	public function windwalker()
	{
		$this->load(__FUNCTION__);
	}

	/**
	 * Set Module and callback.
	 *
	 * @param string   $name
	 * @param callable $handler
	 *
	 * @return  static
	 *
	 * @deprecated  3.0  Use addModule() instead.
	 */
	public function setModule($name, $handler)
	{
		return $this->addModule($name, $handler);
	}

	/**
	 * Add Module callback.
	 *
	 * @param string   $name
	 * @param callable $handler
	 *
	 * @return  static
	 */
	public function addModule($name, $handler)
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

	/**
	 * registerCoreModules
	 *
	 * @return  void
	 */
	protected function registerCoreModules()
	{
		// RequireJS
		$this->addModule('requireJS', function(Module $module, AssetHelper $asset)
		{
			if ($module->inited())
			{
				return;
			}

			$asset->addJs('require.js');
		});

		// Underscore
		$this->addModule('underscore', function(Module $module, AssetHelper $asset, $noConflict = true)
		{
			if (!$module->inited())
			{
				$asset->addJs('underscore.js');
			}

			if (!$module->stateInited() && $noConflict)
			{
				$asset->internalJS(';var underscore = _.noConflict();');
			}
		});

		// Backbone
		$this->addModule('backbone', function(Module $module, AssetHelper $asset, $noConflict = true)
		{
			if (!$module->inited())
			{
				// Dependency
				\JHtmlJquery::framework(true);

				$module->getManager()->underscore();

				$asset->addJs('backbone.js');
			}

			if (!$module->stateInited() && $noConflict)
			{
				$asset->internalJS(';var backbone = Backbone.noConflict();');
			}
		});

		// Windwalker
		$this->addModule('windwalker', function(Module $module, AssetHelper $asset)
		{
			if ($module->inited())
			{
				return;
			}

			$asset->windwalker();
		});
	}
}
