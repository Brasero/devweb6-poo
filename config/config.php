<?php

use App\Framework\Database\DatabaseFactory;
use App\Framework\Renderer\RendererInterface;
use App\Framework\Renderer\TwigRendererFactory;
use App\Framework\Router\Router;
use App\Framework\Router\RouterTwigExtension;
use Doctrine\ORM\EntityManager;

return [
    "doctrine.is_dev_mode" => true,
    "doctrine.driver" => "pdo_mysql",
    "doctrine.user" => "root",
    "doctrine.password" => "",
    "doctrine.db_name" => "devWeb6",
    "config.view_path" =>  dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views',
    "twig.extensions" => [
        RouterTwigExtension::class
    ],
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    Router::class => \DI\create(),
    EntityManager::class => \DI\factory(DatabaseFactory::class)
];
