<?php

/**
 * This file implements the controllers' explorer.
 * The explorer scans all controllers and extract data.
 */

namespace dbeurive\Slim\controller;

use dbeurive\Util\UtilClass;
use DocBlockReader\Reader;

/**
 * Class Explorer
 *
 * This class implements the controllers' explorer.
 * The explorer scans all controllers and extract data.
 * The extracted data is organised within an indexed array.
 * Each element of this indexed array is an associative array (which keys are listed below) that contains information about one controller.
 * Keys that describe a controller:
 *         - Explorer::KEY_DATA_CONTROLLER_PATH
 *         - Explorer::KEY_DATA_CONTROLLER_NAMESPACE
 *         - Explorer::KEY_DATA_CONTROLLER_CLASS_BASE_NAME
 *         - Explorer::KEY_DATA_CONTROLLER_CLASS_FULL_NAME
 *         - Explorer::KEY_DATA_CONTROLLER_URI_PREFIX
 *         - Explorer::KEY_DATA_CONTROLLER_ACTIONS: this key points to an associative array that contains the keys below:
 *           - Explorer::KEY_DATA_ACTION_HTTP_METHOD
 *           - Explorer::KEY_DATA_ACTION_NAME
 *           - Explorer::KEY_DATA_ACTION_CLASS_METHOD
 *           - Explorer::KEY_DATA_ACTION_URI_PARAMS
 *
 * @see Explorer::__getControllerData
 *
 * @package dbeurive\Slim\controller
 */
class Explorer
{
    /**
     * This key points to the path to the controller.
     */
    const KEY_DATA_CONTROLLER_PATH = 'controller-path';
    /**
     * This key points to the controller's namespace.
     */
    const KEY_DATA_CONTROLLER_NAMESPACE = 'controller-namespace';
    /**
     * This key points to the name of the class that implements the controller (without the class namespace).
     * Please note that if the file name of the file that implements the controller is "UserController.php", then the name of the class that implements the controller must be "UserController".
     */
    const KEY_DATA_CONTROLLER_CLASS_BASE_NAME = 'controller-class-base-name';
    /**
     * This key points to the "fully qualified name" of the class that implements the controller.
     * Please note that the "fully qualified name" of the class includes the namespace.
     */
    const KEY_DATA_CONTROLLER_CLASS_FULL_NAME = 'controller-class-full-name';
    /**
     * This key points to the controller's URI prefix (for example "/user/").
     */
    const KEY_DATA_CONTROLLER_URI_PREFIX = 'uri-prefix';
    /**
     * This key points to an array that contains data about all the actions within the controller.
     * These data are represented by the keys that begin with "KEY_DATA_ACTION_".
     *
     * @see Explorer::KEY_DATA_ACTION_NAME
     * @see Explorer::KEY_DATA_ACTION_HTTP_METHOD
     * @see Explorer::KEY_DATA_ACTION_CLASS_METHOD
     * @see Explorer::KEY_DATA_ACTION_URI_PARAMS
     */
    const KEY_DATA_CONTROLLER_ACTIONS = 'controller-actions';
    /**
     * This key points to the name of the action.
     * Let's say that the method's name is "actionPostLogin".
     * Then, the name of the action is "login".
     */
    const KEY_DATA_ACTION_NAME = 'action-name';
    /**
     * This key points the the HTTP method associated with the action.
     * Let's say that the method's name is "actionPostLogin".
     * Then, the HTTP method is "POST".
     */
    const KEY_DATA_ACTION_HTTP_METHOD = 'action-http-method';
    /**
     * The name of the method (within the controller class) that implements the action.
     * For example: "actionPostLogin".
     */
    const KEY_DATA_ACTION_CLASS_METHOD = 'action-class-name';
    /**
     * This key points to the string that represents the Slim's specification for the URI parameters (if any).
     * For example: "/{id}/{age}"
     */
    const KEY_DATA_ACTION_URI_PARAMS = 'action-uri-params';
    /**
     * Name of the annotation, within a DOC comment, that represents the action's list of parameters.
     * Ex: @uri-params {id}
     */
    const ANNOTATION_ACTION_PARAMS = 'uri-params';
    /**
     * @var string Path to the base directory that contains all the controllers.
     *      This path starts with the directory separator ("/" under UNIX, or "\" under Windows).
     *      This path ends with the directory separator.
     */
    private $__baseDirectoryPath;
    /**
     * @var string File suffix that identifies a controller.
     *      For example: "Controller.php".
     *                   This means that the file "UserController.php" contains the class "UserController", which implements the controller "User".
     */
    private $__fileSuffix;
    /**
     * @var int Number of sub directories, below the base directory, where controllers are stored.
     *      Let's say that the base directory (under which all controllers are stored) is "/path/to/base/dir/".
     *
     *      If the value of $__controllerPathDepth is 0:
     *      Then, controllers must be stored within the base directory itself (that is: "/path/to/base/dir/").
     *
     *      If the value of $__controllerPathDepth is 1:
     *      Then, controllers must be stored within one sub directory of the base directory.
     *      For example: "/path/to/base/dir/user/" or "/path/to/base/dir/profile/"
     *
     * @see Explorer::$__baseDirectoryPath
     */
    private $__controllerPathDepth;
    /**
     * @var bool The verbosity flag.
     */
    private $__verbose = false;

    /**
     * Explorer constructor.
     * @param string $inBaseDirectoryPath Path to the base directory to scan (including sub directories).
     * @param string $inControllerSuffix File suffix that identifies controllers.
     * @param int $inOptPathDepth Number of sub directories below the base directory where to find the controllers.
     * @param bool $inOptVerbose This flag specifies whether the explorer should print information about its execution.
     */
    public function __construct($inBaseDirectoryPath, $inControllerSuffix, $inOptPathDepth=0, $inOptVerbose=false)
    {
        $inBaseDirectoryPath = realpath($inBaseDirectoryPath);
        $this->__baseDirectoryPath = DIRECTORY_SEPARATOR . trim($inBaseDirectoryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->__fileSuffix = $inControllerSuffix;
        $this->__controllerPathDepth = $inOptPathDepth;
        $this->__verbose = $inOptVerbose;
    }

    /**
     * Scans all the directories that may contain controllers and returns data.
     * @return array The method returns an array that contains the data extracted from the controllers.
     *         Each element of the returned array is an associative array as returned by the method __getControllerData().
     *         Please see the description of the method __getControllerData().
     * @throws \Exception
     * @see Explorer::__getControllerData for a description of the data that characterizes a controller.
     */
    public function explore()
    {
        $error = null;

        // Find and scan all PHP files that implement the controllers.
        $this->__log("Scanning directory \"" . $this->__baseDirectoryPath . "\" for controllers.");
        $controllers = $this->__findAllControllers($this->__baseDirectoryPath, $this->__fileSuffix);

        $prefixes = array();
        $ctrlData = array();

        /** @var string $_controllerPath */
        foreach ($controllers as $_controllerPath) {

            $this->__log("- Scanning controller \"$_controllerPath\".");

            $data = $this->__getControllerData($_controllerPath);
            $prefix = $data[self::KEY_DATA_CONTROLLER_URI_PREFIX];

            // Sanity checks.
            if (array_key_exists($prefix, $prefixes)) {
                throw new \Exception("Duplicate controller prefix found: \"$prefix\" (in \"$_controllerPath\").");
            }
            $prefixes[$prefix] = $_controllerPath;
            $data[self::KEY_DATA_CONTROLLER_URI_PREFIX] = $prefix;
            $ctrlData[] = $data;
        }

        return $ctrlData;
    }

    /**
     * Return data about a given controller, designed by the path to the PHP file the implements it.
     *
     * @param string $inControllerPath Path to the PHP file the implements the controller.
     * @return array The method returns an associative array that contains the following keys:
     *         - Explorer::KEY_DATA_CONTROLLER_PATH
     *         - Explorer::KEY_DATA_CONTROLLER_NAMESPACE
     *         - Explorer::KEY_DATA_CONTROLLER_CLASS_BASE_NAME
     *         - Explorer::KEY_DATA_CONTROLLER_CLASS_FULL_NAME
     *         - Explorer::KEY_DATA_CONTROLLER_URI_PREFIX
     *         - Explorer::KEY_DATA_CONTROLLER_ACTIONS: this key points to an associative array that contains the keys below:
     *           - Explorer::KEY_DATA_ACTION_HTTP_METHOD
     *           - Explorer::KEY_DATA_ACTION_NAME
     *           - Explorer::KEY_DATA_ACTION_CLASS_METHOD
     *           - Explorer::KEY_DATA_ACTION_URI_PARAMS
     *
     * @see Explorer::KEY_DATA_CONTROLLER_PATH
     * @see Explorer::KEY_DATA_CONTROLLER_NAMESPACE
     * @see Explorer::KEY_DATA_CONTROLLER_CLASS_BASE_NAME
     * @see Explorer::KEY_DATA_CONTROLLER_CLASS_FULL_NAME
     * @see Explorer::KEY_DATA_CONTROLLER_URI_PREFIX
     * @see Explorer::KEY_DATA_CONTROLLER_ACTIONS
     * @see Explorer::KEY_DATA_ACTION_HTTP_METHOD
     * @see Explorer::KEY_DATA_ACTION_NAME
     * @see Explorer::KEY_DATA_ACTION_CLASS_METHOD
     * @see Explorer::KEY_DATA_ACTION_URI_PARAMS
     *
     * @throws \Exception
     */
    private function __getControllerData($inControllerPath) {

        // Please note that we cannot create an instance of the class \ReflectionClass without knowing the fully qualified name of the class (that implements the controller).
        // Therefore, we have to find the namespace of the class first.
        $namespace = UtilClass::get_namespace($inControllerPath);
        if (is_null($namespace)) {
            $namespace = '\\';
        }

        require_once $inControllerPath;

        // Remove the extension of the file (".php", ".php5"...).
        $classBaseName = preg_replace('/\.[^\.]+$/', '', basename($inControllerPath));
        $classFullName = $namespace . '\\' . $classBaseName;

        if (! is_subclass_of($classFullName, \dbeurive\Slim\controller\Controller::class)) {
            throw new \Exception("The controller \"$classFullName\" is not a subclass of \"" . \dbeurive\Slim\controller\Controller::class . '"');
        }

        $controllerReflexion = new \ReflectionClass($classFullName);

        $actions = array();

        /** @var \ReflectionMethod $_method */
        foreach ($controllerReflexion->getMethods() as $_method) {

            $_methodName = $_method->getName();

            if (false !== $action = $this->__isAction($_methodName)) {
                $this->__log("  - Scan method \"$_methodName\".");
                $reader = new Reader($classFullName, $_methodName, 'method');
                $action[self::KEY_DATA_ACTION_CLASS_METHOD] = $_methodName;
                $uriParams = $reader->getParameter(self::ANNOTATION_ACTION_PARAMS);
                $uriParams = preg_replace('/^\//', '', $uriParams);

                $action[self::KEY_DATA_ACTION_URI_PARAMS] = $uriParams;
                $actions[] = $action;
            }
        }

        $uriPrefix = $this->__getDirectoryUriPrefix($inControllerPath);

        // $reader = new Reader($classFullName);
        // $uriPrefix = $reader->getParameter(self::ANNOTATION_CONTROLLER_URI_PREFIX);
        // if (0 == strlen("$uriPrefix")) {
        //     throw new \Exception("Controller \"$inControllerPath\" has no URI prefix.");
        // }
        $uriPrefix = $this->__checkAndPrepareControllerUriPrefix($uriPrefix);

        return array(
            self::KEY_DATA_CONTROLLER_PATH            => $inControllerPath, // The path to the PHP file that implements the controller
            self::KEY_DATA_CONTROLLER_NAMESPACE       => $namespace,        // The namespace
            self::KEY_DATA_CONTROLLER_CLASS_BASE_NAME => $classBaseName,    // The controller's class, without the namespace
            self::KEY_DATA_CONTROLLER_CLASS_FULL_NAME => $classFullName,    // The controller's class, with the namespace
            self::KEY_DATA_CONTROLLER_URI_PREFIX      => $uriPrefix,        // Ex: "/user/"
            self::KEY_DATA_CONTROLLER_ACTIONS         => $actions           // List of actions.
        );
    }

    /**
     * Checks and prepares a given file suffix (that identifies a controller).
     *
     * @param string $inPrefix Suffix to check and prepare.
     * @return string The method returns the suffix.
     * @throws \Exception
     */
    private function __checkAndPrepareControllerUriPrefix($inPrefix) {

        $tokens = preg_split('/\//', $inPrefix, -1,  PREG_SPLIT_NO_EMPTY);
        foreach ($tokens as &$_token) {
            $_token = strtolower($_token);
            if (1 !== preg_match('/^[a-z\-_0-9]+$/i', $_token)) {
                throw new \Exception("Invalid URI prefix \"$inPrefix\".");
            }
        }

        return count($tokens) > 0 ? implode('/', $tokens) : '/';
    }

    /**
     * Find all the controllers.
     * The names of the files that implements the controllers must end by the suffix given by the value of the parameter $inSuffix.
     *
     * @param array $inBaseDirectory Path to the base directory to scan for controllers.
     * @param string $inSuffix Suffix used to recognise a PHP file that contains the implementation of a controller.
     * @return array The method returns a list of absolute paths.
     */
    private function __findAllControllers($inBaseDirectory, $inSuffix) {
        $files = array();

        $keeper = function ($inPath) use ($inSuffix) {
            return substr($inPath, -1*strlen($inSuffix)) === $inSuffix;
        };

        $directory = new \RecursiveDirectoryIterator($inBaseDirectory);
        $iterator = new \RecursiveIteratorIterator($directory);

        /** @var \SplFileInfo $_fileInfo */
        foreach ($iterator as $_fileInfo) {
            $path = $_fileInfo->getRealPath();
            if (call_user_func($keeper, $path)) {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Test if a given method's name (within the controller class) represents an action or not.
     * If the method's name represents an action, then the method returns data about the action:
     * - the name of the action.
     * - the HTTP method associated with the action.
     *
     * Example: let's consider the method which name (within the controller class) is "actionPostLogin".
     *       - The name of the action is "login".
     *       - The HTTP method associated to the action is "POST".
     *
     * @param string $inName Action's name.
     * @return array|bool If the name represents an action, then the method returns an array with two keys:
     *         - Explorer::KEY_DATA_ACTION_NAME
     *         - Explorer::KEY_DATA_ACTION_HTTP_METHOD
     *         Otherwise, the method returns false.
     *
     * @see Explorer::KEY_DATA_ACTION_NAME
     * @see Explorer::KEY_DATA_ACTION_HTTP_METHOD
     */
    private function __isAction($inName) {
        $matches = array();
        if (1 === preg_match('/^action(Get|Post|Put|Delete|Options|Patch|Any)([A-Z].*)$/', $inName, $matches)) {
            $httpMethod = strtolower($matches[1]);
            $action = strtolower($matches[2]);
            return array(
                self::KEY_DATA_ACTION_HTTP_METHOD => $httpMethod,
                self::KEY_DATA_ACTION_NAME => $action
            );
        }
        return false;
    }

    /**
     * Calculate the URL prefix for the controller, base on the path of the file that implements it.
     * @param string $inControllerPath Path to the controller.
     * @return string The URI prefix.
     * @throws \Exception
     */
    private function __getDirectoryUriPrefix($inControllerPath) {
        // $this->__baseDirectoryPath starts and ends with a directory separator.
        // $inControllerPath is an absolute path.
        $pathTail = substr($inControllerPath, strlen($this->__baseDirectoryPath), -1*strlen($this->__fileSuffix));
        $tokens = explode(DIRECTORY_SEPARATOR, $pathTail);
        $depth = count($tokens) - 1;
        if ($depth != $this->__controllerPathDepth) {
            $message = array(
                "Invalid controller path \"$inControllerPath\". Unexpected depth \"$depth\".",
                'It should be "' . $this->__controllerPathDepth . '".',
                "This error means that this controller's file is not stored under the right level of sub directories below the base directory \"" . $this->__baseDirectoryPath . '".'
            );


            throw new \Exception(implode(" ", $message));
        }

        return $pathTail;
    }

    /**
     * Print a message.
     * @param string $inMessage Message to print.
     */
    private function __log($inMessage) {
        if ($this->__verbose) {
            print "$inMessage" . PHP_EOL;
        }
    }
}