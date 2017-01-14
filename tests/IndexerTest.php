<?php

use \dbeurive\Slim\controller\Explorer;

class ExplorerTest extends \PHPUnit_Framework_TestCase
{
    const NUMBER_OF_CONTROLLERS = 2;

    public function testExplore() {
        $explorer = new Explorer(__DIR__ . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'controller0', 'Controller.php');
        $data = $explorer->explore();

        $this->assertInternalType('array', $data);
        $this->assertCount(self::NUMBER_OF_CONTROLLERS, $data);
        for ($i=0; $i<self::NUMBER_OF_CONTROLLERS; $i++) {
            $this->assertArrayHasKey(Explorer::KEY_DATA_CONTROLLER_ACTIONS, $data[$i]);
            $this->assertInternalType('array', $data[$i][Explorer::KEY_DATA_CONTROLLER_ACTIONS]);
        }
    }
}