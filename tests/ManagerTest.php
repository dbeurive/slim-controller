<?php

use dbeurive\Slim\controller\Manager as ControllerManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use dbeurive\Slim\Requester;


class ManagerTest extends \dbeurive\Slim\PHPUnit\TestCase
{
    /** @var /Slim/App */
    private $__application;
    /** @var bool */
    private $__flag;

    public function setUp() {
        $this->__flag = $this->__getDeclareRoutesFlag();
        $configuration = require __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        $configuration[FLAG] = $this->__flag ? "Declare all routes" : "Declare only the required routes";
        $this->__application = new \Slim\App($configuration);
    }

    public function testGet() {

        ControllerManager::$REQUEST_URI = '/user/get/toto';
        ControllerManager::start($this->__application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index.json', $this->__flag);
        $requester = new Requester($this->__application);
        $text = $requester->get('/user/get/toto');

        // TODO: Test the value of $test.
    }


    private function __getDeclareRoutesFlag() {

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


}