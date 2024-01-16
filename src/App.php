<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp;

use Progphil1337\Config\Config;
use ProgPhil1337\DependencyInjection\Injector;
use ProgPhil1337\SimpleReactApp\Console\Console;
use ProgPhil1337\SimpleReactApp\HTTP\Request\RequestHandler;
use ProgPhil1337\SimpleReactApp\HTTP\Request\RequestPipelineHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Socket\SocketServer;

class App
{
    public function __construct(
        private readonly Config   $config,
        private readonly Injector $container
    )
    {
    }

    /**
     * @param class-string<RequestPipelineHandlerInterface>[] $pipeline
     * @return int
     */
    public function run(array $pipeline): int
    {
        $requestHandler = new RequestHandler($this->container, $pipeline);

        $server = new HttpServer(fn(ServerRequestInterface $r): ResponseInterface => $this->handle($r, $requestHandler));
        $server->on('error', fn(\Exception $exception) => Console::error((string)$exception));
        $uri = sprintf('%s:%s', $this->config->get('host'), $this->config->get('port'));

        $socket = new SocketServer($uri);
        $server->listen($socket);
        Console::info('Server running on ' . $uri);

        return 0; // closed successfully
    }

    private function handle(ServerRequestInterface $r, RequestHandler $requestHandler): ResponseInterface
    {
        $response = $requestHandler->handle($r);

        return $response->toHttpResponse();
    }
}
