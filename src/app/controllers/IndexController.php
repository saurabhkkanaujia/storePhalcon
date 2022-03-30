<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        // $this->view->users = Users::find();
        // echo "jhsvdjvjsvd";
    }

    public function signOutAction()
    {
        $this->session->destroy();
        $this->response->redirect('/user/signin');
    }
}
