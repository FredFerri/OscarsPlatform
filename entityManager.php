<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

function getEntityManager() {
    // Create a simple "default" Doctrine ORM configuration for Annotations
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/models"), $isDevMode);


// database configuration parameters
    $conn = array(
        'dbname' => 'oscars',
        'user' => '',
        'password' => '',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
        'path' => __DIR__ . '/db.mysql',
    );

// obtaining the entity manager
    $em = EntityManager::create($conn, $config);
    return $em;
}




