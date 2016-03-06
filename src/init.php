<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

include_once __DIR__ . '/Windwalker.php';

$windwalker = new \Windwalker\Windwalker;

$windwalker->init(defined('WINDWALKER_CONSOLE'));
