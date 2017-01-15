# <a name="a0"></a>Introduction

This package allows the use of "controllers" with the [Slim framework](https://www.slimframework.com/).

# <a name="a1"></a>Synopsis

## <a name="a2"></a>Define the controllers

    $ tree /path
    /path
    └── to
        └── controllers
            ├── ProfileController.php
            └── UserController.php

> Please note the suffix used to create the controllers' file names: "`Controller.php`". This suffix can be changed.

```php
<?php

namespace dbeurive\Slim\Test\controller0;
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
        $response->getBody()->write("Profile has been set! (" . $this->app->getContainer()[FLAG] . ')');
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
```

> Please note the names of the methods that implement the controllers' actions ("actionPostSet" and "actionGetGet").
> Actions' names start with the prefix  "`action`", followed by the name of the expected HTTP method (see the [Slim's documentation](https://www.slimframework.com/docs/objects/router.html)).
>
> Please note the annotation "`@uri-params {id}`" (for the method "`actionGetGet()`"). This means that the URI associated with this action is "`/profile/get/{id}`".
>
> Please have a look to the controller [ProfileController.php](tests/www/controller0/ProfileController.php) and the controller [UserController.php](tests/www/controller0/UserController.php).

## <a name="a3"></a>Create the index that lists all controllers

```bash
php slim-controller.php index --index-path /app/data/index.json /path/to/controllers/
```

> The index "`/app/data/index.json`" contains data about the controllers. Please click [here](doc/index.md) to see the content of this file.
>
> Please click here to see the script [slim-controller.php](bin/slim-controller.php).

## <a name="a4"></a>Implement the Slim application

```php
use dbeurive\Slim\controller\Manager as ControllerManager;

$app = new \Slim\App([]);
ControllerManager::start($app, '/app/data/index.json');
$app->run();
```

> Please note that, within this example, only the actions within the expected controller (depending on the URL) will be declared.
> For example, let's says that the URL is: `http://www.slim-controller.localhost/profile/get/10`.
> The expected controller is "ProfileController".
>
> This behaviour can be modified. Indeed, it is possible to configure the controller manager so that it will systematically register all routes (from all controllers).
> To do that, we should have written: `ControllerManager::start($app, '/app/data/index.json', true);`
>
> Please, click [here](tests/www/index.php) to see the real example.


