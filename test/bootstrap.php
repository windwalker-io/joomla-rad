<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

// We are a valid entry point.
const _JEXEC = 1;

$host = defined('WINDWALKER_TEST_HOST') ? WINDWALKER_TEST_HOST : 'rad.windwalker.io';
$uri = defined('WINDWALKER_TEST_URI') ? WINDWALKER_TEST_URI : '/flower/sakura';

$_SERVER['HTTP_HOST'] = $host;
$_SERVER['REQUEST_URI'] = $uri;
$_SERVER['SCRIPT_NAME'] = $uri;
$_SERVER['PHP_SELF'] = $uri;

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

// Check installed
if (is_dir(JPATH_ROOT . '/installation') || !is_file(JPATH_ROOT . '/configuration.php'))
{
	die('Please install Joomla first.');
}

// Get the framework.
require_once JPATH_BASE . '/includes/framework.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

restore_exception_handler();

// Windwalker init
include_once dirname(__DIR__) . '/src/Windwalker.php';
$windwalker = new \Windwalker\Windwalker;
$windwalker->autoload();

// Prepare TestApplication
$app = new \Windwalker\Test\Application\TestApplication;
\JFactory::$application = $app;

$windwalker->init();

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// System configuration.
$config = new JConfig;
