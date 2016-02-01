<?php
/**
 * Part of joomla330 project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Script;

use Windwalker\Facade\AbstractProxyFacade;
use Windwalker\Helper\AssetHelper;

/**
 * An Asset Manager class help us manage script dependency.
 *
 * @see \Windwalker\Script\ModuleManager
 *
 * @method  static  ModuleManager  getInstance()
 * @method  static  Module         getModule($name)
 * @method  static  ModuleManager  addModule($name, $module)
 * @method  static  AssetHelper    getHelper($option = null)
 * @method  static  boolean        load($name)
 * @method  static  boolean        getModules()
 * @method  static  ModuleManager  setModules($modules)
 * @method  static  boolean        getLegacy()
 * @method  static  ModuleManager  setLegacy($legacy)
 *
 * @since 2.0
 */
class ScriptManager extends AbstractProxyFacade
{
	/**
	 * Property The DI key.
	 *
	 * @var  string
	 */
	protected static $_key = 'script.manager';
}
