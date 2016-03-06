<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

// Install modules
// ========================================================================
$modules = $manifest->modules;

if (!empty($modules))
{
	foreach ($modules as $module)
	{
		// Install per module
		foreach ($module as $var)
		{
			$var          = (string) $var;
			$install_path = dirname($path) . '/modules/' . $var;

			// Do install
			$installer = new JInstaller;

			if ($result[] = $installer->install($install_path))
			{
				$status = $tick;
			}
			else
			{
				$status = $cross;
			}

			// Set success table
			$grid->addRow(array('class' => 'row' . ($i % 2)));
			$grid->setRowCell('num',     ++$i, $tdClass);
			$grid->setRowCell('type',    JText::_('COM_INSTALLER_TYPE_MODULE'), $tdClass);
			$grid->setRowCell('name',    JText::_(strtoupper($var)), array());
			$grid->setRowCell('version', $installer->manifest->version, $tdClass);
			$grid->setRowCell('state',   $status, $tdClass);
			$grid->setRowCell('info',    JText::_($installer->manifest->description), array());
		}
	}
}
