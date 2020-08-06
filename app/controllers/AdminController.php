<?php

namespace App\Controllers;

/**
 * Class AdminController
 * @package App\Controllers
 *
 * @RoutePrefix("/admin")
 */
class AdminController extends ControllerBase
{

    /**
     * @Get("/")
     */
    public function indexAction()
    {
        $this->view->user = $this->session->get('auth');
    }

}

