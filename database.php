<?php

function loadDB() {
    $database = 'oscars';
    $host = 'localhost';
    $user = '';
    $pwd = '';

    $connect = mysqli_connect($host, $user, $pwd, $database);
    if (mysqli_connect_error()) {
        die('Erreur de connexion (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
    }

    else {
        return $connect;
    }
}



?>
