<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response; 

require '../vendor/autoload.php';

$app = new \Slim\App;

$app->get('/helloworld',function(Request $request, Response $response)
{
    $response->getBody()->write("Hello World");
});

require '../src/tvShows.php';
$app->run();