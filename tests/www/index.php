<?php

/**
 * This file implements the WEB service's entry point.
 */

use dbeurive\Slim\controller\Manager as ControllerManager;
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Create the application.
$configuration = require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
$app = new \Slim\App($configuration);

ControllerManager::start($app, __DIR__ . DIRECTORY_SEPARATOR . 'index.json', true);
$app->run();

