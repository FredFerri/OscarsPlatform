<?php


class controller {

    var $twig = null;

    public function loadTwig() {
        $loader = new Twig_Loader_Filesystem('views/');
        $twig = new Twig_Environment($loader, array(
            'cache' => false
        ));
        return $twig;
    }
}

?>