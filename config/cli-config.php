<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// replace with file to your own project bootstrap
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';

if (isset($app)) {
    $container = $app->getContainer();

    $entityManager = $container->get(EntityManager::class);
    $commands = [];

    ConsoleRunner::run(
        new SingleManagerProvider($entityManager),
        $commands
    );
}
