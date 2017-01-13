<?php

/**
 * This file contains the implementation of the controller manager.
 */

namespace dbeurive\Slim\controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Manager
 *
 * This class implements the controller manager.
 *
 * @package dbeurive\Slim\controller
 */
class Manager
{
    /**
     * This key points to the controller's data.
     */
    const KEY_CONTROLLER = 'controller';
    /**
     * This key point the URI prefix.
     */
    const KEY_URI_PREFIX = 'uri-prefix';

    /** @var \Slim\App */
    static private $__app;

    /**
     * Initialize the manager.
     *
     * Please keep in mind that this method is executed once for each request.
     * That is: this method is executed once for each execution of the application.
     *
     * @param \Slim\App $inApplication The Slim application.
     * @param string $inIndexPath Index that lists all available controllers.
     * @param bool $inOptRegisterAllControllers This flag defines how the routes are defined.
     *        - If the value of this parameter is true, then all routes are registered.
     *        - If the value of this parameter is false, then only the routes that applies to the required controller are registered.
     * @throws \Exception
     */
    static public function start($inApplication, $inIndexPath, $inOptRegisterAllControllers=false) {

        self::$__app = $inApplication;

        // Load the index.
        if (false === $json = file_get_contents($inIndexPath)) {
            throw new \Exception("Can not load the file \"$inIndexPath\".");
        }
        $index = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON in file \"$inIndexPath\": " . json_last_error_msg());
        }

        if ($inOptRegisterAllControllers) {
            // Register all routes (for all controllers).
            foreach ($index as $_controllerPrefix => $_controllerData) {
                self::__registerControllerActions(array(self::KEY_CONTROLLER => $_controllerData, self::KEY_URI_PREFIX => $_controllerPrefix));
            }
        } else {
            // Register only the routes that apply to the controller being executed.
            self::$__app->add(function (Request $request, Response $response, $next) use ($index) {
                $controller = Manager::__findController($request, $index);
                self::__registerControllerActions($controller);
                $response = $next($request, $response);
                return $response;
            });
        }
    }

    /**
     * Given the current request, find the controller configuration that applies.
     *
     * @param Request $request The request.
     * @param array $inIndex Index.
     * @return array The method returns an array that contains the following keys:
     *         - Manager::KEY_CONTROLLER: the controller's configuration.
     *         - Manager::KEY_URI_PREFIX: the URI prefix associated with the controller.
     * @throws \Exception
     */
    static private function __findController(Request $request, array $inIndex) {
        $uriPath = $request->getUri()->getPath();

        /**
         * @var string $_uriPrefix Example: "/user/".
         * @var array $_controllerData
         */
        foreach ($inIndex as $_uriPrefix => $_controllerData) {
            $prefix = "/${_uriPrefix}/";

            if (substr($uriPath . '/', 0, strlen($prefix)) === $prefix) {
                return array(
                    self::KEY_CONTROLLER => $_controllerData,
                    self::KEY_URI_PREFIX => $_uriPrefix
                );
            }
        }

        throw new \Exception("Could not find any controller for the following URI: \"$uriPath\".");
    }

    /**
     * Register all actions from a given controller (relatively to a given URI prefix).
     *
     * @param array $inControllerConfiguration Controller's configuration.
     *        This array contains two key:
     *        - Manager::KEY_URI_PREFIX: the URI prefix for the controller (ex: "user" or "ajax/user").
     *        - Manager::KEY_CONTROLLER: the controller's data.
     *
     * @see Indexer for a description of the controller's data.
     */
    static private function __registerControllerActions(array $inControllerConfiguration) {
        $uriPrefix = $inControllerConfiguration[self::KEY_URI_PREFIX];
        $class = $inControllerConfiguration[self::KEY_CONTROLLER][Indexer::KEY_CONTROLLER][Indexer::KEY_CONTROLLER_CLASS];
        $controller = new $class(self::$__app);

        /** @var array $_action */
        foreach ($inControllerConfiguration[self::KEY_CONTROLLER][Indexer::KEY_ACTIONS] as $_action) {
            /** @var string $httpMethod */
            $httpMethod = $_action[Indexer::KEY_HTTP_METHOD];
            $actionUri = $_action[Indexer::KEY_ACTION_URI];
            $_controllerMethod = $_action[Indexer::KEY_METHOD];

            self::$__app->{$httpMethod}("/${uriPrefix}/${actionUri}", function(Request $request, Response $response) use ($controller, $_controllerMethod) {
                return $controller->{$_controllerMethod}($request, $response);
            });
        }
    }
}