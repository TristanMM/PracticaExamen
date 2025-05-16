<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Ruta principal para probar que el contenedor de negocio funciona
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Bienvenido a la capa de negocio");
    return $response;
});


