<?php
/**
 * Part of Component {{extension.name.cap}} files.
 *
 * @copyright   Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

include_once JPATH_COMPONENT_ADMINISTRATOR . '/src/init.php';

if (!class_exists('Windwalker\Windwalker'))
{
	return;
}

echo with(new {{extension.name.cap}}Component)->execute();
