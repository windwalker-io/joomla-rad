<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

$init = JPATH_LIBRARIES . '/windwalker/src/init.php';

if (!is_file($init))
{
	JFactory::getApplication()->enqueueMessage('Windwalker Framework not found.', 'error');

	return false;
}

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

if (is_dir(JPATH_BASE . '/components/{{extension.element.lower}}'))
{
	JLoader::registerPrefix('{{extension.name.cap}}', JPATH_BASE . '/components/{{extension.element.lower}}');
	JLoader::register('{{extension.name.cap}}Component', JPATH_BASE . '/components/{{extension.element.lower}}/component.php');
}

JLoader::registerNamespace('{{extension.name.cap}}', JPATH_ADMINISTRATOR . '/components/{{extension.element.lower}}/src');
JLoader::registerNamespace('Windwalker', __DIR__);

return true;
