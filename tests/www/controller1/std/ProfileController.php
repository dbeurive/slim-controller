<?php

namespace dbeurive\Slim\Test\controller1;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use dbeurive\Slim\controller\Controller;

/**
 * Class ProfileController
 * @package dbeurive\Slim\Test\controller0
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
        $data = $request->getParsedBody();
        $firstName = filter_var($data['firstname'], FILTER_SANITIZE_STRING);
        $lastName  = filter_var($data['lastname'], FILTER_SANITIZE_STRING);
        $response->getBody()->write("Profile $firstName $lastName has been set! (" . $this->app->getContainer()[FLAG] . ')');
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
        $response->getBody()->write("This is the requested profile data (" . $this->app->getContainer()[FLAG] . ')');
        return $response;
    }
}