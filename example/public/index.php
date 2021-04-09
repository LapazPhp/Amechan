<?php
require __DIR__ . '/../../vendor/autoload.php';

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(require __DIR__ . '/../config/di.php');

\Slim\Factory\AppFactory::setContainer($containerBuilder->build());
$app = \Slim\Factory\AppFactory::create();

$app->get('/', function($request, $response) {
    $response->getBody()->write($this->get('view')->render('index'));
    return $response;
});

$app->get('/no-bootstrap-page', function($request, $response) {
    $response->getBody()->write($this->get('view')->render('no-bootstrap-page'));
    return $response;
});

$app->run();
