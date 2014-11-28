<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

JLoader::registerPrefix('{{extension.name.cap}}', JPATH_BASE . '/components/{{extension.element.lower}}');
JLoader::registerNamespace('{{extension.name.cap}}', JPATH_ADMINISTRATOR . '/components/{{extension.element.lower}}/src');
JLoader::registerNamespace('Windwalker', __DIR__);
JLoader::register('{{extension.name.cap}}Component', JPATH_BASE . '/components/{{extension.element.lower}}/component.php');
