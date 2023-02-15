<?php

namespace Config;

use Attribute;
use Neoan\Enums\RequestMethod;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\RouteAttribute;

#[Attribute(Attribute::TARGET_ALL)]
class FormPost implements RouteAttribute
{
    private string $controllerClass;
    private string $templateFile;
    private array $middleWare;
    private string $route;

    public function __construct(string $route, ?string $templateFile = null, ...$middleware)
    {
        $this->templateFile = $templateFile ?? '';
        $this->middleWare = $middleware;
        $this->route = $route;
    }

    public function setControllerClass(string $qualifiedName): void
    {
        $this->controllerClass = $qualifiedName;
    }

    function generateRoute(): void
    {
        $chain = [...$this->middleWare];
        $chain[] = $this->controllerClass;

        \Neoan\Routing\Route::request(RequestMethod::POST, $this->route, ...$chain)
            ->view($this->templateFile)
            ->response([Response::class, 'html']);
    }
}