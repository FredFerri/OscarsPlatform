<?php

namespace Oscars\Controllers;

use controller;

require_once 'controller.php';


class errorController extends controller
{
    public function index()
    {
        $twig = $this->loadTwig();
        $template = $twig->load('error404.html.twig');
        echo $template->render();
    }
}