<?php

use Phalcon\Mvc\Controller;

class TestController extends Controller
{
    public function TestAction()
    {
        $obj = new App\Components\DateHelper();
        echo $obj->getCurrentDate();
    }
}
