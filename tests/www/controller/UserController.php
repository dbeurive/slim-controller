<?php

namespace dbeurive\Slim\Test\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use dbeurive\Slim\controller\Controller;

/**
 * Class UserController
 * @package dbeurive\Slim\Test\controller
 */
class UserController extends Controller
{
    /**
     * Authenticate a user.
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionPostLogin(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $firstName = filter_var($data['firstname'], FILTER_SANITIZE_STRING);
        $lastName  = filter_var($data['lastname'], FILTER_SANITIZE_STRING);
        $response->getBody()->write("Hello, $firstName $lastName");
        return $response;
    }

    /**
     * Get data about a user.
     * @param Request $request
     * @param Response $response
     * @return Response
     * @uri-params {id}
     */
    public function actionGetGet(Request $request, Response $response) {
        $response->getBody()->write("This is the requested user data.");
        return $response;
    }
}