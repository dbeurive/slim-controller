<?php

/**
 *
 * Examples:
 *
 *      php slim-controller0.php index ../tests/www/
 *
 *      php slim-controller0.php dump --host http://domain.com index.json
 *
 */

$autoloadPath = realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php')));
require_once $autoloadPath;

use dbeurive\Slim\bin\application\Indexer as AppIndexer;
use dbeurive\Slim\bin\application\Dumper as AppDumper;
use Symfony\Component\Console\Application;

$application = new Application();
$application->setAutoExit(true);
$application->add(new AppIndexer());
$application->add(new AppDumper());
$application->run();

exit(0);
