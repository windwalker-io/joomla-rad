<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

include_once JPATH_COMPONENT_ADMINISTRATOR . '/src/init.php';

if (!class_exists('Windwalker\Windwalker'))
{
	return;
}

echo with(new {{extension.name.cap}}Component)->execute();
