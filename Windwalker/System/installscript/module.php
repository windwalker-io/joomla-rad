<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

// Set module install success info
$grid->addRow(array('class' => 'row' . ($i % 2)));
$grid->setRowCell('num',     ++$i, $td_class);
$grid->setRowCell('type',    JText::_('COM_INSTALLER_TYPE_MODULE'), $td_class);
$grid->setRowCell('name',    JText::_(strtoupper($manifest->name)), array());
$grid->setRowCell('version', $manifest->version, $td_class);
$grid->setRowCell('state',   $tick, $td_class);
$grid->setRowCell('info',    '', array());
