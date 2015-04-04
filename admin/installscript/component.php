<?php
/**
 * @package     Windwalker.Framework
 * @subpackage  Install.Script
 * @author      Simon Asika <asika32764@gmail.com>
 * @copyright   Copyright (C) 2013 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Set component install success info
$grid->addRow(array('class' => 'row' . ($i % 2)));
$grid->setRowCell('num', ++$i, $tdClass);
$grid->setRowCell('type', JText::_('COM_INSTALLER_TYPE_COMPONENT'), $tdClass);
$grid->setRowCell('name', JText::_(strtoupper($manifest->name)), array());
$grid->setRowCell('version', $manifest->version, $tdClass);
$grid->setRowCell('state', $tick, $tdClass);
$grid->setRowCell('info', '', array());
