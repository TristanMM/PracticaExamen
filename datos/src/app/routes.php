<?php
    namespace App\controllers;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    use Slim\Routing\RouteCollectorProxy;

    $app->get('/', function (Request $request, Response $response, $args) {
        $response->getBody()->write("FCB 9 - 2 RVAR");
        return $response;
    });
    

    $app->group('/api',function(RouteCollectorProxy $api){
        $api->group('/socios',function(RouteCollectorProxy $producto){
            $producto->get('/read[/{id}]', Socios::class . ':read');
            $producto->post('', Socios::class . ':create');
            $producto->put('/{id}', Socios::class . ':update');
            $producto->delete('/{id}', Socios::class . ':delete');
            $producto->get('/filtrar', Socios::class . ':filtrar');
            $producto->get('/filtrarDos/{pag}/{lim}',Socios::class . ':filtrarDos'); //filtrar
        });
    });
