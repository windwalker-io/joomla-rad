<?php
/**
 * @package     Joomla.Site
 * @subpackage  {{extension.element.lower}}
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

include_once JPATH_COMPONENT_ADMINISTRATOR . '/src/init.php';

echo with(new {{extension.name.cap}}Component)->execute();
