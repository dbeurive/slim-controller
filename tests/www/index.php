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
// Check the environment.
// ------------------------------------------------------------------

checkIndex();

// ------------------------------------------------------------------
// Create the application.
// ------------------------------------------------------------------

$configuration = require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

$flag = getDeclareRoutesFlag();
$configuration[FLAG] = $flag ? "Declare all routes" : "Declare only the required routes";

$app = new \Slim\App($configuration);
ControllerManager::start($app, __DIR__ . DIRECTORY_SEPARATOR . 'index0.json', $flag);
$app->run();



function checkIndex() {
    $indexPath = __DIR__ . DIRECTORY_SEPARATOR . 'index0.json';
    if (! file_exists($indexPath)) {
        throw new \Exception("The index file \"$indexPath\" has not been created! Please create it first! You can run the unit tests to do that.");
    }
}

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

