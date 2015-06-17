<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

// Set component install success info
$grid->addRow(array('class' => 'row' . ($i % 2)));
$grid->setRowCell('num',     ++$i, $tdClass);
$grid->setRowCell('type',    JText::_('COM_INSTALLER_TYPE_COMPONENT'), $tdClass);
$grid->setRowCell('name',    JText::_(strtoupper($manifest->name)), array());
$grid->setRowCell('version', $manifest->version, $tdClass);
$grid->setRowCell('state',   $tick, $tdClass);
$grid->setRowCell('info',    '', array());

// Render Information
echo '<h1>' . JText::_(strtoupper($manifest->name)) . '</h1>';

$img  = JURI::base() . '/components/' . strtolower($manifest->name) . '/images/' . strtolower($manifest->name) . '_logo.png';
$img  = JHtml::_('image', $img, 'LOGO');
$link = JRoute::_("index.php?option=" . $manifest->name);

echo '<div id="ak-install-img">' . JHtml::link($link, $img) . '</div>';
echo '<div id="ak-install-msg">' . JText::_(strtoupper($manifest->name) . '_INSTALL_MSG') . '</div>';
echo '<br /><br />';
