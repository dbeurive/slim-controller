<?php

/**
 * This file implements the WEB service's entry point.
 */

use dbeurive\Slim\controller\Manager as ControllerManager;
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// ------------------------------------------------------------------
// Errors and exceptions handlers.
// ------------------------------------------------------------------

function exception_handler(\Exception $exception) {
    file_put_contents(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'exceptions.txt', $exception->getMessage() . "\n");
}

set_exception_handler('exception_handler');

function error_handler($errno, $errstr, $errfile, $errline)
{
    file_put_contents(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'errors.txt', $errstr . "\n");
    return true;
}

set_error_handler("error_handler");

// ------------------------------------------------------------------
// Create the application.
// ------------------------------------------------------------------

$configuration = require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

if (array_key_exists('REQUEST_URI', $_SERVER)) {
    // Only executed while testing with a WEB browser.
    $flag = getDeclareRoutesFlag();
    $configuration[FLAG] = $flag ? "Declare all routes" : "Declare only the required routes";
}

$app = new \Slim\App($configuration);
ControllerManager::start($app, __DIR__ . DIRECTORY_SEPARATOR . 'index.json', $flag);
$app->run();





function getDeclareRoutesFlag() {
    $flagFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'flag.txt';

    if (file_exists($flagFile)) {
        if (false === unlink($flagFile)) {
            throw new \Exception("Can not delete the file $flagFile");
        }
    } else {
        if (false === file_put_contents($flagFile, '1')) {
            throw new \Exception("Can not create the file $flagFile");
        }
        if (false === chmod($flagFile, 0777)) {
            throw new \Exception("Can not change mode for the file $flagFile");
        }
    }
    return file_exists($flagFile);
}

