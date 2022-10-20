<?php

use App\Framework\App;
use App\Framework\Renderer\PHPRenderer;
use App\Framework\Renderer\TwigRenderer;
use App\Home\HomeModule;
use GuzzleHttp\Psr7\ServerRequest;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function Http\Response\send;

require dirname(__DIR__)."/vendor/autoload.php";

$modules = [
    HomeModule::class
];

$loader = new FilesystemLoader(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');
$twig = new Environment($loader, []);

$renderer = new TwigRenderer($loader, $twig);

$app = new App($modules, [
    "renderer" => $renderer
]);

$response = $app->run(ServerRequest::fromGlobals());

send($response);