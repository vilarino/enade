<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
//    return $app['twig']->render('index.html.twig', array());

    $a = new \Controller\Import($app);
    $msg = $a->importCategories();

    /*$msg = $a->importCities();*/
    /*$msg = $a->importCountry();*/

    var_dump($msg);
    exit;

})->bind('homepage');

$app->get('/import', function () use ($app) {
    $message = $app['importManager.controller']->import();
    return $app['twig']->render('import.html.twig', array(
        'message' => $message
    ));
})->bind('import');

$app->get('/load', function () use ($app) {
    $message = $app['load.controller']->run();
    var_dump($message); exit;
})->bind('load');

$app->get('/region', function () use ($app) {
    /*$sql = "SELECT * FROM DIM_REGIAO";
    $items = $app['dbs']['enade_dw']->fetchAll($sql);
    var_dump($items);
    exit;*/
})->bind('region');

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/' . $code . '.html.twig',
        'errors/' . substr($code, 0, 2) . 'x.html.twig',
        'errors/' . substr($code, 0, 1) . 'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
