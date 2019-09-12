<?php
ini_set('error_reporting', E_ALL);
;
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Make sure it recognizes that we're testing.
$_SERVER['CI_ENVIRONMENT'] = 'testing';
define('ENVIRONMENT', 'testing');

// Load our paths config file
require __DIR__ . '/../vendor/codeigniter4/framework/app/Config/Paths.php';

// path to the directory that holds the front controller (index.php)
define('FCPATH', realpath(__DIR__ . '/../vendor/codeigniter4/framework/') . '/public' . DIRECTORY_SEPARATOR);

// The path to the "tests" directory
define('TESTPATH', realpath(__DIR__) . DIRECTORY_SEPARATOR);

define('SUPPORTPATH', realpath(__DIR__ . '/../vendor/codeigniter4/framework/tests/_support/') . DIRECTORY_SEPARATOR);

// Set environment values that would otherwise stop the framework from functioning during tests.
if (! isset($_SERVER['app.baseURL']))
{
	$_SERVER['app.baseURL'] = 'http://example.com';
}

//--------------------------------------------------------------------
// Load our TestCase
//--------------------------------------------------------------------

require  SUPPORTPATH . '/CIUnitTestCase.php';
