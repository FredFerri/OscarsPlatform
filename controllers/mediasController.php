<?php

namespace Oscars\Controllers;

use controller;
use Models\Media;
use Models\Model;
use Models\Profile;
use \Models\User;

include($_SERVER["DOCUMENT_ROOT"] . "/models/User.php");
include($_SERVER["DOCUMENT_ROOT"] . "/database.php");
include($_SERVER["DOCUMENT_ROOT"] . "/controllers/controller.php");
include($_SERVER["DOCUMENT_ROOT"] . "/entityManager.php");

class mediasController extends controller
{

    public function index()
    {

        session_start();
        if (!array_key_exists('mdp', $_SESSION)) {
            header('Location:/');
        }

        $first_page = false;
        $last_page = false;

        $url_id = $_SERVER['REQUEST_URI'];
        $page_id = preg_replace('/[^0-9]+/', '', $url_id);

        $media = new Media();
        $listMedias = $media->getAll('Media');

        $totalNumber = count($listMedias);
        if ($totalNumber >= 100) {
            $pagesNumber = ceil($totalNumber / 120);
        }
        else {
            $pagesNumber = ceil($totalNumber / 12);
        }

        $listMedias = array_slice($listMedias, -12);

        //On affiche seulement 12 images par page
        $limit = $page_id * 12;
        $offset = $limit - 12;
        $listMedias = $media->limitedRequestMedia($offset);

        //On efface le bouton "précédent" si on se trouve sur la 1ere page, idem pour la dernière
        if ($page_id == 1) {
            $first_page = true;
        }

        if ($page_id == $pagesNumber) {
            $last_page = true;
        }

        $twig = $this->loadTwig();
        $template = $twig->load('medias.html.twig');
        echo $template->render(array('listMedias' => $listMedias, 'session' => $_SESSION,
            'first_page' => $first_page, 'last_page' => $last_page, 'page_id' => $page_id));
    }

    public function addMedia()
    {
        if (!empty($_FILES)) {
            session_start();

            $db = loadDB();

            $title = mysqli_real_escape_string($db, $_POST['title']);
            $description = mysqli_real_escape_string($db, $_POST['description']);
            $date = mysqli_real_escape_string($db, $_POST['date']);
            $date = date('d-m-Y', strtotime(str_replace('-', '/', $date)));
            $place = mysqli_real_escape_string($db, $_POST['place']);

            $user_id = $_SESSION['id'];
            $author = new User();
            $author = $author->getEntityById('User', $user_id);

            $request = ['title' => $title, 'description' => $description, 'date' => $date, 'place' => $place];

            /* Transfert du fichier vers le serveur */

            $ds          = DIRECTORY_SEPARATOR;

            $storeFolder = '../medias/images';

            $tempFile = $_FILES['file']['tmp_name'];

            $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;

            $targetFile =  $targetPath. $_FILES['file']['name'];

            move_uploaded_file($tempFile,$targetFile);

            //

            $request['URI'] = '/medias/images/'.$_FILES['file']['name'];

            $media = new Media($request);
            $result = $media->addMedia();

            if ($result === true) {
                $media->setUserId($author);
                $media->editMedia();
            }
            else {
                echo "Erreur d'enregistrement du média";
            }

            return true;

        }

        return false;

    }

    public function editMedia($id)
    {

        function validateDate($date)
        {
            $d = \DateTime::createFromFormat('d-m-Y', $date);
            return $d && $d->format('d-m-Y') === $date;
        }

        $db = loadDB();
        $id = str_replace("'", "", $id);
        $id = substr($id,1);

        $title = mysqli_real_escape_string($db, $_POST['title']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        $date= mysqli_real_escape_string($db, $_POST['date']);
        if (validateDate($date)) {
            $date = date('d-m-Y', strtotime(str_replace('-', '/', $date)));
        }
        else {unset($date);}
        $place= mysqli_real_escape_string($db, $_POST['place']);

        $request = ['title' => $title, 'description' => $description, 'place' => $place];

        if (!empty($date)) {
            $request['date'] = $date;
        }

        $class = new Media();
        $media = $class->getEntityById('Media', $id);
        $media->hydrate($request);
        $result = $media->editMedia();

        echo $result;
        sleep(3);
        header('Location:/media/page/:1');

    }

    public function deleteMedia($id)
    {
        $id = str_replace("'", "", $id);
        $id = substr($id,1);
        $model = new Media();
        $media = $model->removeEntity('Media',$id);
        if ($media) {
            header('Location:/media/page/:1');
        }
        else {
            echo "Photo introuvable";
        }
    }
}