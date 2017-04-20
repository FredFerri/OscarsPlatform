<?php

namespace Oscars\Controllers;

use controller;
use Models\User;

require_once 'controller.php';

include($_SERVER["DOCUMENT_ROOT"] . "/entityManager.php");


class indexController extends controller {

    public function index()
    {
        //Si l'utilisateur est authentifié, on le redirige vers la page 'Wall', sinon, vers la page 'login'
        session_start();
        $twig = $this->loadTwig();
        if (array_key_exists('mdp', $_SESSION)) {
            header('Location:/wall');
        }
        else {
            $template = $twig->load('login.html.twig');
            echo $template->render(array('moteur_name' => 'Twig'));
        }

    }

    public function login()
    {
        $pseudo = $_POST['pseudo'];
        $password = $_POST['password'];
        $class = new User();
        if (!$user = $class->getEntityByName('User', $pseudo)) {
            return false;
        }
        else {
            $original_password = $user->getPassword();
            if (password_verify($password, $original_password)) {
                session_start();
                $_SESSION['mdp'] = "OK";
                $_SESSION['id'] = $user->getId();
                $_SESSION['profile'] = $user->getProfileId();
                echo 'OK';
            }
            else {
                return false;
            }
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location:/');
    }

}

