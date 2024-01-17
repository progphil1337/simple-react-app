<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline;

use FastRoute\Dispatcher;
use ProgPhil1337\DependencyInjection\Injector;
use ProgPhil1337\SimpleReactApp\HTTP\Request\RequestPipelineHandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RoutingService;
use Psr\Http\Message\ServerRequestInterface;

class RoutingPipelineHandler implements RequestPipelineHandlerInterface
{
    public function __construct(
        private readonly RoutingService $routingService,
        private readonly Injector       $container
    )
    {

    }

    public function __invoke(ServerRequestInterface $request): false|ResponseInterface
    {
        $uri = $request->getRequestTarget();

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->routingService->dispatch(HttpMethod::fromIgnoreCase($request->getMethod()), $uri);

        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => false,
            Dispatcher::METHOD_NOT_ALLOWED => new JSONResponse(['error' => 'Method not allowed']),
            Dispatcher::FOUND => $this->callHandler($routeInfo[1], $routeInfo[2], $request)
        };
    }

    private function callHandler(string $fqcn, array $vars, ServerRequestInterface $serverRequest): ResponseInterface
    {
        /** @var HandlerInterface $handler */
        $handler = $this->container->create($fqcn);

        return $handler($serverRequest, new RouteParameters($vars));
    }
}
