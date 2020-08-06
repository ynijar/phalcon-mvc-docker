<?php

namespace Common\Plugins;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Component;
use Phalcon\Acl\Adapter\Memory as AclList;

/**
 * Class ApiAcl
 * @package Common\Plugins
 */
class ApiAcl extends BasePlugin
{
    /**
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {

        $acl = new AclList();
        $acl->setDefaultAction(Acl\Enum::DENY);

        foreach ($this->config->acl->roles as $roleName => $roleParams) {
            $inherits = (isset($roleParams->inherits)) ? $roleParams->inherits : null;
            if (is_object($inherits)) {
                foreach ($inherits as $item) {
                    $acl->addRole(new Role($roleName), $item);
                }
            } else {
                $acl->addRole(new Role($roleName), $inherits);
            }
        }

        foreach ($this->config->acl->resources as $resource => $actions) {
            $resource = strtolower($resource);
            $actions = $actions->toArray();
            $acl->addComponent(new Component($resource), array_map(function ($action) {
                return strtolower($action);
            }, array_keys($actions)));
            foreach ($actions as $action => $allows) {
                foreach ($allows as $allowRole) {
                    $acl->allow($allowRole, $resource, strtolower($action));
                }
            }
        }

        return $acl;
    }

    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return mixed
     */
    public function afterExecuteRoute(Event $event, Dispatcher $dispatcher)
    {

        $auth = $this->session->get('auth');

        $user = null;
        $account = ['roles' => ['Guest']];

        if (!empty($auth)) {
            $user = $auth;
        } else {
            $user = null;
        }

        if ($user !== null) {
            $account['roles'][] = ucfirst(strtolower($user['role']));
            $this->getDI()->set('user', $user, true);
        } else {
            $this->getDI()->set('user', function () {
                return null;
            }, true);
        }


        $controllerEx = explode("\\", $dispatcher->getControllerName());

        $controller = strtolower(str_replace('_', '', $controllerEx[count($controllerEx) - 1]));

        $action = strtolower($dispatcher->getActionName());

        $acl = $this->getAcl();

        foreach ($account['roles'] as $singleRole) {
            if ($acl->isAllowed($singleRole, $controller, $action) == Acl\Enum::ALLOW) {
                return true;
            }
        }

        if ($dispatcher->getControllerName() === 'index' && $action === 'index') {
            return true;
        }

        $this->response->redirect("/");
        return false;
    }
}
