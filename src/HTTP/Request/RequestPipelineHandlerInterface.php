<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Request;

use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestPipelineHandlerInterface
{
    public function __invoke(ServerRequestInterface $request): false|ResponseInterface;
}
