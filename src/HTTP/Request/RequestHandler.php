<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Request;

use ProgPhil1337\DependencyInjection\Injector;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestHandler
{
    /** @var RequestPipelineHandlerInterface[] $pipeline */
    private readonly array $pipeline;

    /**
     * @param Injector $container
     * @param class-string<RequestPipelineHandlerInterface>[] $pipelineClasses
     */
    public function __construct(
        Injector $container,
        array    $pipelineClasses = []
    )
    {
        $pipeline = [];
        foreach ($pipelineClasses as $fqcn) {
            $pipeline[] = $container->create($fqcn);
        }

        $this->pipeline = $pipeline;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $current = 0;
        $pipelineLength = count($this->pipeline);

        do {
            $pipe = $this->pipeline[$current++]($request);
        } while ($pipe === false && $current < $pipelineLength);

        return $pipe;
    }
}
