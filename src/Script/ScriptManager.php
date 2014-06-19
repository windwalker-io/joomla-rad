<?php
/**
 * Part of joomla330 project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Script;

use Windwalker\DI\Container;
use Windwalker\Helper\AssetHelper;

/**
 * An Asset Manager class help us manage script dependency.
 *
 * @since 2.0
 */
class ScriptManager
{
	/**
	 * THe asset helpers storage.
	 *
	 * @var  AssetHelper[]
	 */
	protected static $assetHelpers = array();

	/**
	 * The module initialised.
	 *
	 * @var  boolean[]
	 */
	protected static $initialised = array();

	/**
	 * Modules handler storage.
	 *
	 * @var  callable[]
	 */
	protected static $modules = array();

	/**
	 * Load RequireJS.
	 *
	 * @return  void
	 */
	public static function requireJS()
	{
		if (!empty(static::$initialised['requirejs']))
		{
			return;
		}

		$asset = $asset = static::getHelper();

		$asset->addJs('require.js');

		static::$initialised['requirejs'] = true;
	}

	/**
	 * Load underscore.
	 *
	 * @param boolean $noConflict Enable underscore no conflict mode.
	 *
	 * @return  void
	 */
	public static function underscore($noConflict = true)
	{
		if (!empty(static::$initialised['underscore']))
		{
			return;
		}

		$asset = $asset = static::getHelper();

		$asset->addJs('underscore.js');

		if ($noConflict)
		{
			$asset->internalJS(';var underscore = _.noConflict();');
		}

		static::$initialised['underscore'] = true;
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
	public static function backbone($noConflict = false)
	{
		if (!empty(static::$initialised['backbone']))
		{
			return;
		}

		// Dependency
		\JHtmlJquery::framework(true);
		static::underscore();

		$asset = $asset = static::getHelper();

		$asset->addJs('backbone.js');

		if ($noConflict)
		{
			$asset->internalJS(';var backbone = Backbone.noConflict();');
		}

		static::$initialised['backbone'] = true;
	}

	/**
	 * Load Windwalker script.
	 *
	 * @return  void
	 */
	public static function windwalker()
	{
		if (!empty(static::$initialised['windwalker']))
		{
			return;
		}

		static::getHelper()->windwalker();

		static::$initialised['windwalker'] = true;
	}

	/**
	 * Set Module and callback.
	 *
	 * @param string   $name
	 * @param callable $handler
	 *
	 * @return  void
	 */
	public static function setModule($name, $handler)
	{
		$name = strtolower($name);

		static::$modules[$name] = $handler;
	}

	/**
	 * load
	 *
	 * @param string $name Module name.
	 *
	 * @return  boolean
	 */
	public static function load($name)
	{
		$name = strtolower($name);

		if (empty(static::$modules[$name]))
		{
			$app = Container::getInstance()->get('app');

			$app->enqueueMessage(sprintf('Asset module: %s not found.', $name));

			return false;
		}

		if (! is_callable(static::$modules[$name]))
		{
			$app = Container::getInstance()->get('app');

			$app->enqueueMessage(sprintf('Asset module: %s is not callable.', $name));

			return false;
		}

		if (!empty(static::$initialised[$name]))
		{
			return true;
		}

		call_user_func_array(static::$modules[$name], array($name, static::getHelper()));

		static::$initialised[$name] = true;

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
	public static function __callStatic($name, $args = array())
	{
		if (strpos($name, 'load') === 0)
		{
			$name = substr($name, 4);
		}
		else
		{
			return false;
		}

		return static::load(strtolower($name));
	}

	/**
	 * Get AssetHelper by option name.
	 *
	 * @param   string $option Option name.
	 *
	 * @return  AssetHelper
	 */
	public static function getHelper($option = 'windwalker')
	{
		if (!empty(static::$assetHelpers[$option]))
		{
			return static::$assetHelpers[$option];
		}

		try
		{
			$asset = Container::getInstance($option)->get('helper.asset');
		}
		catch (\UnexpectedValueException $e)
		{
			$asset = new AssetHelper($option);
		}

		return static::$assetHelpers[$option] = $asset;
	}
}
