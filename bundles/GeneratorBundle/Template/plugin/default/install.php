<?php
/**
 * @package        {ORGANIZATION}.Plugin
 * @subpackage     {{plugin.group.lower}}.plg_{{extension.name.lower}}
 * @copyright      Copyright (C) 2016 {ORGANIZATION}, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later.
 */

use Joomla\CMS\Installer\Adapter\PluginAdapter;

defined('_JEXEC') or die;

/**
 * Script file of {{extension.name.cap}} Plugin.
 *
 * @since  1.0
 */
class Plg{{plugin.group.cap}}{{extension.name.cap}}InstallerScript
{
	const TYPE_INSTALL = 'install';
	const TYPE_UPDATE = 'update';
	const TYPE_DISCOVER_INSTALL = 'discover_install';

	/**
	 * Method to install the extension.
	 *
	 * @param PluginAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function install(PluginAdapter $parent)
	{
	}

	/**
	 * Method to uninstall the extension.
	 *
	 * @param PluginAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function uninstall(PluginAdapter $parent)
	{
	}

	/**
	 * Method to uninstall the extension.
	 *
	 * @param PluginAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function update(PluginAdapter $parent)
	{
	}

	/**
	 * Method to run before an install/update/uninstall method.
	 *
	 * @param string         $type   Install type (install, update, discover_install, extension_site).
	 * @param PluginAdapter  $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function preflight($type, PluginAdapter $parent)
	{
	}

	/**
	 * Method to run after an install/update/uninstall method.
	 *
	 * @param string        $type   Install type (install, update, discover_install, extension_site).
	 * @param PluginAdapter $parent Parent installer adapter.
	 *
	 * @return  void
	 */
	public function postflight($type, PluginAdapter $parent)
	{
	}
}
