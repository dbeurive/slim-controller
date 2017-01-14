<?php

require_once implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));
define('FLAG', 'flag');

// Create the index.

/*
if ('cli' ==  php_sapi_name()) {
    $args = array(
        'php',
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'slim-controller.php'),
        'index',
        '--' . \dbeurive\Slim\bin\application\Indexer::CLO_INDEX_PATH,
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index.json'),
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'controller0')
    );

    $cmd = implode(' ', $args);
    print "Create the index:\n\n$cmd\n\n";

    exec($cmd);
}
*/

