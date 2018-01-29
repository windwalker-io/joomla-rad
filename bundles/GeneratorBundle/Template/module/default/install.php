<?php
/**
 * @package        {ORGANIZATION}.Module
 * @subpackage     {{extension.element.lower}}
 * @copyright      Copyright (C) 2016 {ORGANIZATION}, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later.
 */

use Joomla\CMS\Installer\Adapter\ModuleAdapter;

defined('_JEXEC') or die;

/**
 * Script file of {{extension.name.cap}} Module.
 *
 * @since  1.0
 */
class Mod_{{extension.name.cap}}InstallerScript
{
	const TYPE_INSTALL = 'install';
	const TYPE_UPDATE = 'update';
	const TYPE_DISCOVER_INSTALL = 'discover_install';

	/**
	 * Method to install the extension.
	 *
	 * @param ModuleAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function install(ModuleAdapter $parent)
	{
	}

	/**
	 * Method to uninstall the extension.
	 *
	 * @param ModuleAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function uninstall(ModuleAdapter $parent)
	{
	}

	/**
	 * Method to uninstall the extension.
	 *
	 * @param ModuleAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function update(ModuleAdapter $parent)
	{
	}

	/**
	 * Method to run before an install/update/uninstall method.
	 *
	 * @param string        $type   Install type (install, update, discover_install).
	 * @param ModuleAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function preflight($type, ModuleAdapter $parent)
	{
	}

	/**
	 * Method to run after an install/update/uninstall method.
	 *
	 * @param string        $type   Install type (install, update, discover_install).
	 * @param ModuleAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function postflight($type, ModuleAdapter $parent)
	{
	}
}
