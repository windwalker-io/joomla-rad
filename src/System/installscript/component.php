<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

// Set component install success info
$grid->addRow(array('class' => 'row' . ($i % 2)));
$grid->setRowCell('num',     ++$i, $tdClass);
$grid->setRowCell('type',    Text::_('COM_INSTALLER_TYPE_COMPONENT'), $tdClass);
$grid->setRowCell('name',    Text::_(strtoupper($manifest->name)), array());
$grid->setRowCell('version', $manifest->version, $tdClass);
$grid->setRowCell('state',   $tick, $tdClass);
$grid->setRowCell('info',    '', array());

// Render Information
echo '<h1>' . Text::_(strtoupper($manifest->name)) . '</h1>';

$img  = Uri::base() . '/components/' . strtolower($manifest->name) . '/images/' . strtolower($manifest->name) . '_logo.png';
$img  = HTMLHelper::_('image', $img, 'LOGO');
$link = JRoute::_("index.php?option=" . $manifest->name);

echo '<div id="ak-install-img">' . HTMLHelper::link($link, $img) . '</div>';
echo '<div id="ak-install-msg">' . Text::_(strtoupper($manifest->name) . '_INSTALL_MSG') . '</div>';
echo '<br /><br />';
