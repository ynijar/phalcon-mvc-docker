<?php

namespace App;

use Common\Plugins\ApiAcl;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Config\Adapter\Json;

/**
 * Class Module
 * @package Api
 */
class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(\Phalcon\Di\DiInterface $di = null)
    {
        $di->getLoader()->registerNamespaces(require_once APP_PATH . '/config/namespaces.php', TRUE)->register();
    }

    /**
     * @param \Phalcon\Di\DiInterface $di
     */
    public function registerServices(\Phalcon\Di\DiInterface $di)
    {
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            return $view;
        });

        $config = $di->get('config');
        $config->merge(new Json(APP_PATH . '/config/acl.json'));

        $di['dispatcher'] = function () use ($di) {
            $dispatcher = new Dispatcher();
            /**
             * @var \Phalcon\Events\Manager $eventsManager
             */
            $eventsManager = $di->getShared('eventsManager');
            $eventsManager->attach('dispatch', new ApiAcl($di));
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        };
    }

}
