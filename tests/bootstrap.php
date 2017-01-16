<?php

require_once implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));
define('FLAG', 'flag');

// Create the indexes.

if ('cli' ==  php_sapi_name()) {

    $lines = array();
    $status = null;

    // -----------------------------------------------
    // Scan www/controller0
    // -----------------------------------------------

    $args = array(
        'php',
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'slim-controller.php'),
        'index',
        '--' . \dbeurive\Slim\bin\application\Indexer::CLO_INDEX_PATH,
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json'),
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'controller0')
    );

    $cmd = implode(' ', $args);
    print "Create the index:\n\n$cmd\n\n";
    exec($cmd, $lines, $status);

    if (0 !== $status) {
        print "ERROR: the command below\n\n:$cmd\n\nfailed!\n\n";
        exit(1);
    }

    // -----------------------------------------------
    // Scan www/controller1
    // -----------------------------------------------

    $args = array(
        'php',
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'slim-controller.php'),
        'index',
        '--' . \dbeurive\Slim\bin\application\Indexer::CLO_INDEX_PATH,
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index1.json'),
        '--' . \dbeurive\Slim\bin\application\Indexer::CLO_PATH_DEPTH,
        1,
        escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'controller1')
    );

    $cmd = implode(' ', $args);
    print "Create the index:\n\n$cmd\n\n";
    exec($cmd, $lines, $status);

    if (0 !== $status) {
        print "ERROR: the command below\n\n:$cmd\n\nfailed!\n\n";
        exit(1);
    }

}

