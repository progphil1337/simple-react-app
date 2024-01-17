<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler;

use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

interface HandlerInterface
{
    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface;
}
