<?php

use Phalcon\Mvc\Router\Annotations;

$di->set('router', function () {
    $router = new Annotations(FALSE);
    $router->removeExtraSlashes(TRUE);

    $router->addModuleResource('app', 'App\Controllers\Index');
    $router->addModuleResource('app', 'App\Controllers\Admin');
    $router->addModuleResource('app', 'App\Controllers\Home');


    $router->setDefaultModule('app');
    $router->setDefaultController('App\Controllers\Index');

    $router->setDefaultAction('index');

    $router->notFound([
        'module' => 'app',
        'controller' => 'App\Controllers\Index',
        'action' => 'index'
    ]);
    return $router;
});
