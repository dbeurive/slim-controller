<?php

namespace dbeurive\Slim\Test\controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use dbeurive\Slim\controller\Controller;

/**
 * Class ProfileController
 * @package dbeurive\Slim\Test\controller
 */
class ProfileController extends Controller
{
    /**
     * Create or update a profile.
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionPostSet(Request $request, Response $response) {
        $response->getBody()->write("Profile has been set!");
        return $response;
    }

    /**
     * Get a profile.
     * @param Request $request
     * @param Response $response
     * @return Response
     * @uri-params {id}
     */
    public function actionGetGet(Request $request, Response $response) {
        $response->getBody()->write("This is the requested profile data");
        return $response;
    }
}