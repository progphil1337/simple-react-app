<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline;

use ProgPhil1337\SimpleReactApp\HTTP\Request\RequestPipelineHandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use Psr\Http\Message\ServerRequestInterface;

class DefaultRequestPipelineHandler implements RequestPipelineHandlerInterface
{
    public function __invoke(ServerRequestInterface $request): JSONResponse
    {
        return new JSONResponse(['error' => 'Not found'], 404);
    }
}
