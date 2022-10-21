<?php

namespace App\Blog;

use App\Entity\Article;
use App\Entity\Category;
use App\Framework\Module;
use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\Router;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule extends Module
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    private EntityManager $manager;

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . DIRECTORY_SEPARATOR . 'views');
        $this->router->get("/blog", [$this, 'index'], 'blog.index');
        $this->router->get("/blog/show/{id:\d+}", [$this, 'show'], 'blog.show');
        $this->router->get('/blog/delete/{id:\d+}', [$this, 'delete'], 'blog.delete');
        $this->router->get('/blog/create', [$this, 'create'], 'blog.create');
        $this->router->post('/blog/create', [$this, 'create']);
        $this->router->get('/blog/update/{id:\d+}', [$this, 'update'], 'blog.update');
        $this->router->post('/blog/update/{id:\d+}', [$this, 'update']);
        $this->manager = $manager;
    }


    public function index(ServerRequestInterface $request)
    {
        $articles = $this->manager->getRepository(Article::class)->findAll();
        return $this->renderer->render('@blog/index', [
            "articles" => $articles
        ]);
    }

    public function show(ServerRequestInterface $request)
    {
        $params = $request->getAttributes();
        $id = $params['id'];
        $article = $this->manager->getRepository(Article::class)->find($id);
        return $this->renderer->render('@blog/show', [
            "article" => $article
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(ServerRequestInterface $request)
    {
        $method = $request->getMethod();
        if ($method == 'POST') {
            $data = $request->getParsedBody();
            $cat = $this->manager->getRepository(Category::class)->find(1);
            $article = new Article();
            $date = new \DateTime();
            $article->setTitre($data['titre'])
                ->setContent($data['content'])
                ->setCategory($cat)
                ->setCreatedAt($date)
                ->setUpdatedAt($date);

            $this->manager->persist($article);
            $this->manager->flush();
        }

        return $this->renderer->render('@blog/create');
    }

    public function delete(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $article = $this->manager->getRepository(Article::class)->find($id);
        $this->manager->remove($article);
        $this->manager->flush();
        return (new \GuzzleHttp\Psr7\Response())
                ->withHeader('Location', '/blog')
                ->withStatus(301);
    }

    public function update(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $article = $this->manager->getRepository(Article::class)->find($id);
        $method = $request->getMethod();

        if ($method == 'POST') {
            $date = new \DateTime();
            $data = $request->getParsedBody();
            $article->setTitre($data['titre'])
                ->setContent($data['content'])
                ->setUpdatedAt($date);
            $this->manager->flush();
            return (new \GuzzleHttp\Psr7\Response())
                ->withHeader('Location', '/blog')
                ->withStatus(301);
        }

        return $this->renderer->render('@blog/update', [
            "article" => $article
        ]);
    }
}
