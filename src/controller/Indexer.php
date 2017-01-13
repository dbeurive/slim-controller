<?php

/**
 * This file implements the indexer.
 */

namespace dbeurive\Slim\controller;

/**
 * Class Indexer
 *
 * This class implements in indexer.
 * The indexer generates the index from the data extracted from all the controllers.
 * The index is an associative array which:
 * - keys are URI prefixes associated with the controllers (ex: "user", "ajax/user"...).
 * - values are controllers' data. These are associative arrays that contain the following keys:
 *   - Indexer::KEY_CONTROLLER: data about the controller.
 *     This associative array contains the keys below:
 *     - Indexer::KEY_CONTROLLER_CLASS
 *     - Indexer::KEY_CONTROLLER_PATH
 *   - Indexer::KEY_ACTIONS: list of actions for this controller.
 *     Each element of the list is an associative array that contains the keys listed below:
 *     - Indexer::KEY_HTTP_METHOD
 *     - Indexer::KEY_ACTION_URI
 *     - Indexer::KEY_METHOD
 *
 * @see Indexer::KEY_CONTROLLER
 * @see Indexer::KEY_CONTROLLER_CLASS
 * @see Indexer::KEY_CONTROLLER_PATH
 * @see Indexer::KEY_ACTIONS
 * @see Indexer::KEY_HTTP_METHOD
 * @see Indexer::KEY_ACTION_URI
 * @see Indexer::KEY_METHOD
 *
 * @package dbeurive\Slim\controller
 */
class Indexer
{
    /**
     * This keys is used within the generated index.
     * It points to an array that contains data about a controller:
     * - the fully qualified name of the class that implements the controller (key KEY_CONTROLLER_CLASS).
     * - the path to the PHP file that implements the controller (key KEY_CONTROLLER_PATH).
     */
    const KEY_CONTROLLER = 'controller';
    /**
     * This keys is used within the generated index.
     * It points to an array that contains data about actions.
     * Each element of the array describes an action.
     */
    const KEY_ACTIONS = 'actions';
    /**
     * This key is used within the generated index.
     * It identifies the HTTP method ("GET", "POST"...) assigned to the action.
     */
    const KEY_HTTP_METHOD = 'http-method';
    /**
     * This key is used within the generated index.
     * It identifies the URI associated to the action.
     */
    const KEY_ACTION_URI = 'action-uri';
    /**
     * This key is used within the generated index.
     * It identifies the controller class.
     */
    const KEY_CONTROLLER_CLASS = 'class';
    /**
     * This key is used within the generated index.
     * It identifies the method that implements the action.
     */
    const KEY_METHOD = 'method';
    /**
     * This key is used within the generated index.
     * It identifies the path to the file that implements the controller.
     */
    const KEY_CONTROLLER_PATH = 'path';

    /**
     * Generate the index from the controllers' data.
     * @param array $inControllerData data extracted from the controller.
     * @return array The method returns the index.
     * @throws \Exception
     * @see Indexer
     */
    static public function index(array $inControllerData)
    {
        $error = null;

        // Build the index.
        $index = array();
        /** @var array $_data */
        foreach ($inControllerData as $_data) {
            $ctrlPrefix = $_data[Explorer::KEY_DATA_CONTROLLER_URI_PREFIX];
            $actions = $_data[Explorer::KEY_DATA_CONTROLLER_ACTIONS];
            $index[$ctrlPrefix] = array(
                self::KEY_CONTROLLER => array(
                    self::KEY_CONTROLLER_CLASS => '\\' . $_data[Explorer::KEY_DATA_CONTROLLER_CLASS_FULL_NAME],
                    self::KEY_CONTROLLER_PATH  => $_data[Explorer::KEY_DATA_CONTROLLER_PATH]
                ),
                self::KEY_ACTIONS => array()
            );

            /** @var array $_action */
            foreach ($actions as $_action) {
                $params = $_action[Explorer::KEY_DATA_ACTION_URI_PARAMS]; // Ex: "/{firstname}/{lastname}"
                $params = strlen($params) == 0 ? $_action[Explorer::KEY_DATA_ACTION_NAME] : $_action[Explorer::KEY_DATA_ACTION_NAME] . '/' . $params;

                $index[$ctrlPrefix][self::KEY_ACTIONS][] = array(
                    self::KEY_HTTP_METHOD => $_action[Explorer::KEY_DATA_ACTION_HTTP_METHOD],
                    self::KEY_ACTION_URI  => $params,
                    self::KEY_METHOD      => $_action[Explorer::KEY_DATA_ACTION_CLASS_METHOD]
                );
            }
        }

        return $index;
    }
}