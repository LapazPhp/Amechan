<?php
require __DIR__ . '/../../vendor/autoload.php';

$app = new Slim\App();
$container = $app->getContainer();
require __DIR__ . '/../config/di.php';

$app->get('/', function($request, $response) {
    return $this->view->render($response, 'index');
});

$app->get('/no-bootstrap-page', function($request, $response) {
    return $this->view->render($response, 'no-bootstrap-page');
});

$app->run();
