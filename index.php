<?php

require_once "vendor/autoload.php";

require 'Router.class.php';

$router = Router::getInstance();
//Définition du dossier contenant les controlleurs
$router->setPath('controllers/');
$router->addRule('/', array('controller' => 'indexController', 'action' => 'index'));
$router->addRule('login', array('controller' => 'indexController', 'action' => 'login'));
$router->addRule('logout', array('controller' => 'indexController', 'action' => 'logout'));
$router->addRule('eror', array('controller' => 'errorController', 'action' => 'index'));
$router->addRule('registration', array('controller' => 'userController', 'action' => 'registration'));
$router->addRule('users', array('controller' => 'userController', 'action' => 'index'));
$router->addRule('adduser', array('controller' => 'userController', 'action' => 'addUser'));
$router->addRule('edituser', array('controller' => 'userController', 'action' => 'editUser'));
$router->addRule('deluser/:id', array('controller' => 'userController', 'action' => 'deleteUser'));
$router->addRule('user/:id', array('controller' => 'userController', 'action' => 'uniqueUser'));
$router->addRule('media/page/:id', array('controller' => 'mediasController', 'action' => 'index'));
$router->addRule('addmedia', array('controller' => 'mediasController', 'action' => 'addMedia'));
$router->addRule('editmedia/:id', array('controller' => 'mediasController', 'action' => 'editMedia'));
$router->addRule('delmedia/:id', array('controller' => 'mediasController', 'action' => 'deleteMedia'));
$router->addRule('wall', array('controller' => 'wallController', 'action' => 'index'));
$router->addRule('wall/:id', array('controller' => 'wallController', 'action' => 'pagination'));
$router->addRule('addpost', array('controller' => 'wallController', 'action' => 'addPost'));
$router->addRule('deletepost/:id', array('controller' => 'wallController', 'action' => 'deletePost'));
$router->addRule('editpost', array('controller' => 'wallController', 'action' => 'editPost'));
$router->addRule('adduserpic', array('controller' => 'userController', 'action' => 'addUserPic'));
$router->load();











