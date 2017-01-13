<?php

/**
 * This file implements the base class for all controllers.
 */

namespace dbeurive\Slim\controller;

/**
 * Class Controller
 *
 * This class is the base class for all controllers.
 *
 * @package dbeurive\Slim\controller
 */
class Controller
{
    /** @var \Slim\App */
    protected $app;

    /**
     * Controller constructor.
     * @param \Slim\App $inApp The Slim application.
     */
    public function __construct(\Slim\App $inApp)
    {
        $this->app = $inApp;
    }
}