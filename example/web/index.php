<?php
require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../config/app.php';
assert($app instanceof \Silex\Application);

$app->get('/', function() use ($app) {
    return $app['plates']->render('index');
});

$app->get('/no-bootstrap-page', function() use ($app) {
    return $app['plates']->render('no-bootstrap-page');
});

$app->run();
