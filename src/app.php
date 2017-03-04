<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new Provider\YamlConfigConnection());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => $app['connection']
));

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

//$app['csv.controller'] = function () use ($app) {
//    return new \Controller\CSV($app);
//};

$app['importManager.controller'] = function () use ($app) {
    return new \Controller\ImportManager($app);
};

$app['load.controller'] = function () use ($app) {
    return new \Controller\Load($app);
};


$app['mysql.controller'] = function () use ($app) {
    return new \Controller\MySql($app);
};

return $app;
