<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Script file of HelloWorld component
 *
 * @package     Joomla.Administrator
 * @subpackage  {{extension.element.lower}}
 */
class Com_{{extension.name.cap}}InstallerScript
{
	/**
	 * Method to install the component.
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function install(\JInstallerAdapterComponent $parent)
	{
	}

	/**
	 * Method to uninstall the component.
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function uninstall(\JInstallerAdapterComponent $parent)
	{
	}

	/**
	 * Method to update the component
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function update(\JInstallerAdapterComponent $parent)
	{
	}

	/**
	 * ethod to run before an install/update/uninstall method
	 *
	 * @param string                     $type
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function preflight($type, \JInstallerAdapterComponent $parent)
	{
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param string                     $type
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function postflight($type, \JInstallerAdapterComponent $parent)
	{
		$db = JFactory::getDbo();

		// Get install manifest
		// ========================================================================
		$p_installer = $parent->getParent();
		$installer   = new JInstaller;
		$manifest    = $p_installer->manifest;
		$path        = $p_installer->getPath('source');
		$result      = array();

		$css = <<<CSS
<style type="text/css">
#ak-install-img
{
}

#ak-install-msg
{
}
</style>
CSS;

		echo $css;

		$installScript = dirname($path) . '/windwalker/src/System/installscript.php';

		if (!is_file($installScript))
		{
			$installScript = JPATH_LIBRARIES . '/windwalker/src/System/installscript.php';
		}

		include $installScript;
	}
}
