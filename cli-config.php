<?php
// cli-config.php
require_once "entityManager.php";
$entityManager = getEntityManager();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
