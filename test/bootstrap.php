<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

// We are a valid entry point.
const _JEXEC = 1;

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/../../defines.php'))
{
	require_once dirname(__DIR__) . '/../../defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', realpath(dirname(__DIR__) . '/../..'));
	require_once JPATH_BASE . '/includes/defines.php';
}

define('WINDWALKER_CONSOLE', __DIR__);

// Get the framework.
require_once JPATH_BASE . '/includes/framework.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

restore_exception_handler();

// Windwalker init
include_once WINDWALKER_CONSOLE . '/../src/init.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// System configuration.
$config = new JConfig;

// Some environment override
$_SERVER['HTTP_HOST'] = 'rad.windwalker.io';
