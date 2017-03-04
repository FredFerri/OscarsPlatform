<?php

namespace Oscars\Controllers;

use controller;
use \Models\Model;
use \Models\Profile;
use \Models\User;
use \Models\Publication;

include($_SERVER["DOCUMENT_ROOT"] . "/models/User.php");
include($_SERVER["DOCUMENT_ROOT"] . "/database.php");
include($_SERVER["DOCUMENT_ROOT"] . "/controllers/controller.php");
include($_SERVER["DOCUMENT_ROOT"] . "/entityManager.php");

class wallController extends controller
{

    public function index()
    {
        session_start();

        if (!array_key_exists('mdp', $_SESSION)) {
            header('Location:/');
        }

        else {
            $posts = new Publication();
            $listPosts = $posts->getAll('Publication');
            /* Affichage de seulement 10 posts par page */
            $totalNumber = count($listPosts);
            if ($totalNumber >= 100) {
                $pagesNumber = ceil($totalNumber / 100);
            }
            else {
                $pagesNumber = ceil($totalNumber / 10);
            }
            $listPosts = array_slice($listPosts, -10);
            //
            $twig = $this->loadTwig();
            $template = $twig->load('index.html.twig');
            echo $template->render(array('listPosts' => $listPosts, 'pagesNumber' => $pagesNumber, 'session' => $_SESSION));
        }
    }

    public function addPost()
    {
        session_start();
        $db = loadDB();
        $content = mysqli_real_escape_string($db, $_POST['content']);
        $content = str_replace('\r\n','<br>',$content);
        $date = date('Y-m-d H:i:s');
        $date = new \DateTime($date);
        $author_id = $_SESSION['id'];
        $class = new User();
        $parameters = ['content' => $content, 'date' => $date];

        $publication = new Publication($parameters);
        $result = $publication->addPublication();

        if ($result) {
            $author = $class->getEntityById('User', $author_id);
            $publication->setAuthor($author);
            $publication->editPublication();
            $author = $publication->getAuthor();
            $post_id = $publication->getId();
            echo json_encode(array('content' => $content, 'date' => $publication->getDate(), 'name' => $author->getName(), 'picture' => $author->getPicture(),
                'author_id' => $author_id, 'post_id' => $post_id));
        }
        else {
            return false;
        }
    }

    public function editPost() {
        $id = $_POST['id'];
        $content = $_POST['newContent'];
        $class = new Publication();
        $post = $class->getEntityById('Publication', $id);
        $post->setContent($content);
        $result = $post->editPublication();
        if ($result) {
            $newContent = $post->getContent();
            echo $newContent;
        }
        else {
            return false;
        }
    }

    public function deletePost()
    {
        var_dump($_POST);
        $id = $_POST['id'];
        $class = new Publication();
        $post = $class->getEntityById('Publication', $id);
        if (is_object($post)) {
            $post->removeEntity('Publication', $id);
            echo "OK";
        }
        else {
            return false;
        }
    }

    public function pagination()
    {
        session_start();
        $numPage = $_POST['numPage'];
        if ($numPage < 1) {
            echo 'Problème';
        }
        else {
            $limit = $numPage * 10;
            $offset = $limit - 10;
            $publication = new Publication();
            $results = $publication->limitedRequestPublication($offset);
            for ($i=0;$i<count($results);$i++) {
                $user_id = $results[$i]['user_id'];
                $user = new User();
                $user = $user->getEntityById('User', $user_id);
                $picture = $user->getPicture();
                $user_name = $user->getName();
                $results[$i]['picture'] = $picture;
                $results[$i]['name'] = $user_name;
            }
            array_push($results, $_SESSION['id']);
            $results = array_reverse($results);
            echo json_encode($results);
        }

    }

}