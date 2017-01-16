<?php

use dbeurive\Slim\controller\Manager as ControllerManager;
use dbeurive\Slim\Requester;

class ManagerTest extends \dbeurive\Slim\PHPUnit\TestCase
{
    /** @var array */
    private $__configuration;

    public function setUp() {
        $this->__configuration = require __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
    }

    public function _testGet() {

        // The tests below may seem duplicated. They are not.
        // - One test is executed with $flag = true.
        // - One test is executed with $flag = false.
        //
        // Please note:
        // Since unit tests bypass the WEB server, $_SERVER[SERVER_URI] is not SET.
        // In the case where we ask the manager to declare only the required route ($flag = false), we need to tell it about the requested URI.

        $flag = true; // Register ALL routes
        $this->__configuration[FLAG] = "Declare all routes";

        $application = new \Slim\App($this->__configuration);
        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->get('/user/get/toto');
        $this->assertStringStartsWith("This is the requested user data", $text);
        $this->assertResponseIsOk($requester->getResponse());

        // =============================================

        $flag = false;
        $this->__configuration[FLAG] = "Declare only the required routes";
        $application = new \Slim\App($this->__configuration);

        ControllerManager::$REQUEST_URI = '/user/get/toto';
        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->get('/user/get/toto');
        $this->assertStringStartsWith("This is the requested user data", $text);
        $this->assertResponseIsOk($requester->getResponse());

        // =============================================

        $flag = true;
        $this->__configuration[FLAG] = "Declare all routes";
        $application = new \Slim\App($this->__configuration);

        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->get('/user/profile/10');
        $this->assertStringStartsWith("This is the requested user data", $text);
        $this->assertResponseIsOk($requester->getResponse());

        // =============================================

        $flag = false;
        $this->__configuration[FLAG] = "Declare only the required routes";
        $application = new \Slim\App($this->__configuration);

        ControllerManager::$REQUEST_URI = '/user/profile/10';
        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->get('/user/profile/10');
        $this->assertStringStartsWith("This is the requested user data", $text);
        $this->assertResponseIsOk($requester->getResponse());
    }

    public function testPost() {

        // The tests below may seem duplicated. They are not.
        // - One test is executed with $flag = true.
        // - One test is executed with $flag = false.
        //
        // Please note:
        // Since unit tests bypass the WEB server, $_SERVER[SERVER_URI] is not SET.
        // In the case where we ask the manager to declare only the required route ($flag = false), we need to tell it about the requested URI.


        $flag = true;
        $this->__configuration[FLAG] = "Declare all routes";

        $params = array(
            'firstname' => 'Mickey',
            'lastname' => 'Mouse'
        );

        $application = new \Slim\App($this->__configuration);
        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->post('/user/login', $params);
        $this->assertEquals("Hello, Mickey Mouse (Declare all routes)", $text);
        $this->assertResponseIsOk($requester->getResponse());

        // =============================================

        $flag = false;
        $this->__configuration[FLAG] = "Declare only the required routes";
        $application = new \Slim\App($this->__configuration);

        $params = array(
            'firstname' => 'Mickey',
            'lastname' => 'Mouse'
        );

        ControllerManager::$REQUEST_URI = '/user/login';
        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->post('/user/login', $params);
        $this->assertEquals("Hello, Mickey Mouse (Declare only the required routes)", $text);
        $this->assertResponseIsOk($requester->getResponse());

        // =============================================

        $flag = true;
        $this->__configuration[FLAG] = "Declare all routes";
        $application = new \Slim\App($this->__configuration);

        $params = array(
            'firstname' => 'Mickey',
            'lastname' => 'Mouse'
        );

        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->post('/profile/set', $params);
        $this->assertEquals("Profile Mickey Mouse has been set! (Declare all routes)", $text);
        $this->assertResponseIsOk($requester->getResponse());

        // =============================================

        $flag = false;
        $this->__configuration[FLAG] = "Declare only the required routes";
        $application = new \Slim\App($this->__configuration);

        $params = array(
            'firstname' => 'Mickey',
            'lastname' => 'Mouse'
        );

        ControllerManager::$REQUEST_URI = '/profile/set';
        ControllerManager::start($application, __DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index0.json', $flag);
        $requester = new Requester($application);
        $text = $requester->post('/profile/set', $params);
        $this->assertEquals("Profile Mickey Mouse has been set! (Declare only the required routes)", $text);
        $this->assertResponseIsOk($requester->getResponse());
    }
}