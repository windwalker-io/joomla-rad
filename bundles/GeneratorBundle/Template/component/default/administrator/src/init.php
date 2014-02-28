<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/Windwalker/init.php';

JLoader::registerPrefix('{{extension.name.cap}}', JPATH_BASE . '/components/{{extension.element.lower}}');
JLoader::registerNamespace('{{extension.name.cap}}', JPATH_ADMINISTRATOR . '/components/{{extension.element.lower}}/src');
JLoader::registerNamespace('Windwalker', __DIR__);
JLoader::register('{{extension.name.cap}}Component', JPATH_BASE . '/components/{{extension.element.lower}}/component.php');
