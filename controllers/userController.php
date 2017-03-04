<?php

namespace Oscars\Controllers;

use controller;
use Models\Model;
use Models\Profile;
use \Models\User;

include($_SERVER["DOCUMENT_ROOT"] . "/models/User.php");
include($_SERVER["DOCUMENT_ROOT"] . "/database.php");
include($_SERVER["DOCUMENT_ROOT"] . "/controllers/controller.php");
include($_SERVER["DOCUMENT_ROOT"] . "/entityManager.php");

class userController extends controller
{

    //Photo de profil attribuée automatiquement si l'utilisateur n'en upload pas
    private $defaultPicture = '/medias/images/no-profile-pic.jpg';

    public function index()
    {
        //Page 'users' (liste d'utilisateurs, accessible seulement au profil Admin)
        session_start();

        if (!array_key_exists('mdp', $_SESSION)) {
            header('Location:/error');
        }

        else {
            $user = new User();
            $listUsers = $user->getAll('User');
            $twig = $this->loadTwig();
            $template = $twig->load('users.html.twig');
            echo $template->render(array('listUsers' => $listUsers, 'session' => $_SESSION));
        }

    }

    public function uniqueUser()
    {
        //Page 'User/:id'
        // Si l'id récupérée dans l'URL ne correspond pas à l'id de la session en cours on renvoit vers 404, sinon on affiche la page
        session_start();
        $url_id = $_SERVER['REQUEST_URI'];
        $url_id= preg_replace('/[^0-9]+/', '', $url_id);
        if ($_SESSION['id'] == $url_id || $_SESSION['profile'] ==1) {
            $user = new User();
            $userInfos = $user->getEntityById('User', $url_id);
            $profile = new Profile();
            $listProfiles= $profile->getProfiles();
            $twig = $this->loadTwig();
            $template = $twig->load('user.html.twig');
            echo $template->render(array('userInfos' => $userInfos, 'listProfiles' => $listProfiles, 'session' => $_SESSION));
        }
        else {
            header('Location:/error');
        }

    }

    public function registration()
    {
        $twig = $this->loadTwig();
        $template = $twig->load('registration.html.twig');
        echo $template->render();
    }

    public function addUser() {

        $connection = loadDB();

        $model = new Model();

        if (!$model->getEntityByName('User', $_POST['name'])) {
            $name = mysqli_real_escape_string($connection, $_POST['name']);
        }
        else {
            echo json_encode(array('message' => '<p class="alert-error" style="font-size: 14px; text-align: center; margin: auto;">Ce pseudo est déja pris</p>'));
            die;
        }
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        $password = password_hash($password, PASSWORD_BCRYPT);

        if (isset($_FILES['picture'])) {
            $directory = $_SERVER["DOCUMENT_ROOT"].'/medias/images/';
            $uploadfile = $directory.basename($_FILES['picture']['name']);
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
                $picture = '/medias/images/'.$_FILES['picture']['name'];
            }
            else {
                $response = json_encode(array('message' => '<p class="alert-error" style="font-size: 20px;">Impossible de transférer la photo de profil sur le serveur</p>'));;
            }
        }

        $request = ['name' => $name, 'password' => $password];

        if (isset($picture))
        {
            $request['picture'] = $picture;
        }
        else {
            $request['picture'] = $this->defaultPicture;
        }

        $user = new User($request);
        $picturePath = $user->getPicture();
        $result = $user->addUser();


        if ($result === true) {
            echo json_encode(array('message' => '<p class="alert-success" style="font-size: 18px; text-align: center; margin: auto; background-color: #2E363F">Utilisateur ajouté !</p>', 'picturepath' => $picturePath));
        }
        else {
            if (isset($response)) {
                echo $response;
            }
            else {
                echo json_encode(array('message' => '<p class="alert-error" style="font-size: 20px;">Erreur lors de l\'enregistrement</p>'));
            }
        }
    }

    public function editUser()
    {
        $connection = loadDB();

        $id = $_POST['id'];
        $name = mysqli_real_escape_string($connection, $_POST['name']);
        $password = $_POST['password'];
        $email = (isset($_POST['email'])) ? mysqli_real_escape_string($connection, $_POST['email']) : NULL;
        $last_classement = (isset($_POST['last_classement'])) ? mysqli_real_escape_string($connection, $_POST['last_classement']) : NULL;
        $statut = (isset($_POST['statut'])) ? mysqli_real_escape_string($connection, $_POST['statut']) : NULL;
        $profile = (isset($_POST['profile'])) ? mysqli_real_escape_string($connection, $_POST['profile']) : NULL;


        if (isset($_FILES['picture'])) {
            $directory = $_SERVER["DOCUMENT_ROOT"].'/medias/images/';
            $uploadfile = $directory.basename($_FILES['picture']['name']);
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
                $picture = '/medias/images/'.$_FILES['picture']['name'];
            }
            else {
                $response = json_encode(array('message' => '<p class="alert-error" style="font-size: 20px;">Impossible de transférer la photo de profil sur le serveur</p>'));
            }
        }

        $request = ['id' => $id, 'name' => $name, 'email' => $email, 'Last_classement' => $last_classement, 'statut' => $statut];

        /* Si le password est modifié lors de l'édition, on met à jour, sinon on passe */
        if (strlen($password) != 60) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $request['password'] = $password;
        }

        if (isset($picture)) {
            $request['picture'] = $picture;
        }


        $class = new User;
        $user = $class->getEntityById('User', $id);

        if (isset($profile)) {
            $profileObject = new Profile();
            $profileObject = $profileObject->getEntityById('Profile', $profile);
        }

        if (!$user) {
            echo json_encode(array('message' => '<p class="alert-error" style="font-size: 20px;">L\'utilisateur que vous cherchez n\'existe pas</p>'));
        }
        else {
            $updated_user = $user->hydrate($request);
            if (isset ($profileObject)) {
                $user->setProfile($profileObject);
            }
            $result = $updated_user->editUser();
            $picturePath = $updated_user->getPicture();
        }

        if ($result) {
            echo json_encode(array('message' => '<p class="alert-success" style="font-size: 20px;">Utilisateur mis à jour !</p>', 'picturepath' => $picturePath));
        }
        else {
            if (isset($response)) {
                echo $response;
            }
            else {
                echo json_encode(array('message' => '<p class="alert-error" style="font-size: 20px;">Erreur lors de l\'enregistrement</p>'));
            }
        }

    }

    public function deleteUser($id)
    {
        $id = str_replace("'", "", $id);
        $id = substr($id,1);
        $model = new User();
        $media = $model->getEntityById('User',$id);
        if (is_object($media)) {
            $media->removeEntity('User', $id);
            header('Location:/users');
        }
        else {
            echo "User introuvable";
        }
    }

}

