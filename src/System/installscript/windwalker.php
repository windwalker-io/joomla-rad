<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

// Install WindWalker
// ========================================================================
$installer    = new JInstaller;
$installPath = dirname($path) . '/windwalker';

// Version compare
$windwalkerXMLPath = JPATH_LIBRARIES . '/windwalker/windwalker.xml';
$installWindwalker  = true;

// If inner windwalker exists, compare versions.
if (is_file($windwalkerXMLPath) && is_file($installPath . '/windwalker.xml'))
{
	$class = 'SimpleXMLElement';

	if (class_exists('JXMLElement'))
	{
		$class = 'JXMLElement';
	}

	$windwalkerXML = simplexml_load_file($windwalkerXMLPath, $class);
	$installXML    = simplexml_load_file($installPath . '/windwalker.xml', $class);

	if (version_compare((string) $installXML->version, (string) $windwalkerXML->version, '<='))
	{
		$installWindwalker = false;
	}
}
elseif (!is_dir($installPath))
{
	$installPath = JPATH_LIBRARIES . '/windwalker';

	$installWindwalker = false;
}

// Do install
if ($installWindwalker)
{
	if ($result[] = $installer->install($installPath))
	{
		$status = $tick;
	}
	else
	{
		$status = $cross;
	}

	include_once __DIR__ . '/../Installer/WindwalkerInstaller.php';

	\Windwalker\System\Installer\WindwalkerInstaller::install(JPATH_ROOT);

	// Set success table
	$grid->addRow(array('class' => 'row' . ($i % 2)));
	$grid->setRowCell('num',     ++$i, $tdClass);
	$grid->setRowCell('type',    JText::_('COM_INSTALLER_TYPE_LIBRARY'), $tdClass);
	$grid->setRowCell('name',    JText::_('LIB_WINDWALKER'), array());
	$grid->setRowCell('version', $installer->manifest->version, $tdClass);
	$grid->setRowCell('state',   $status, $tdClass);
	$grid->setRowCell('info',    JText::_($installer->manifest->description), array());
}
