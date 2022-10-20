<?php
namespace App\Home;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\Router;
use GuzzleHttp\Psr7\ServerRequest;

class HomeModule
{

    private $renderer;

    private Router $router;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->renderer->addPath('home', __DIR__. DIRECTORY_SEPARATOR . 'view');
        $this->router->get(
            "/",
            [$this, 'index'],
            "home.index"
        );

        $this->router->get(
            "/home/hello/{name:[a-zA-Z]+}",
            [$this, 'hello'],
            'home.hello'
        );
    }

    public function index(ServerRequest $request): string
    {
        $this->renderer->addGlobal("router", $this->router);
        return $this->renderer->render("@home/index");
    }

    public function hello(ServerRequest $request): string
    {
        $name = $request->getAttribute('name');
        return $this->renderer->render("@home/test", [
            "nom" => $name
        ]);
    }
}
