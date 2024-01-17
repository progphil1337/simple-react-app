<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\FileSystem\Pipeline;

use ProgPhil1337\SimpleReactApp\FileSystem\Exception\UnservableFileException;
use ProgPhil1337\SimpleReactApp\FileSystem\FileSystemService;
use ProgPhil1337\SimpleReactApp\FileSystem\Response\FileResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Request\RequestPipelineHandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FileSystemPipelineHandler implements RequestPipelineHandlerInterface
{
    public function __construct(private readonly FileSystemService $fileSystemService)
    {
    }

    public function __invoke(ServerRequestInterface $request): false|ResponseInterface
    {
        try {
            $file = $this->fileSystemService->getFile(
                str_replace(
                    '/',
                    DIRECTORY_SEPARATOR,
                    substr($request->getUri()->getPath(), 1
                    )
                )
            );
        } catch (UnservableFileException) {
            return false;
        }

        return new FileResponse($file);
    }
}
