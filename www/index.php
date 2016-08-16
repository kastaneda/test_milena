<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function () use ($app) {
    return '<h1>Hello, world!</h1>';
});

$app->run();
