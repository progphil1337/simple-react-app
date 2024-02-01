<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Progphil1337\Config\Config;
use ProgPhil1337\SimpleReactApp\Console\Console;
use ProgPhil1337\SimpleReactApp\FileSystem\FileSystemService;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ReflectionClass;

use function FastRoute\cachedDispatcher;

class RoutingService
{
    private readonly Dispatcher $dispatcher;


    public function __construct(private readonly FileSystemService $fileSystemService, private readonly Config $config)
    {
        $cacheFile = PROJECT_PATH . DIRECTORY_SEPARATOR . $config->get('routing::cache');

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        $this->dispatcher = cachedDispatcher(function (RouteCollector $r) {
            foreach ($this->getHandlers() as $fqcn) {
                $reflectionClass = new ReflectionClass($fqcn);
                $reflectionAttributes = $reflectionClass->getAttributes(Route::class);

                if (!count($reflectionAttributes)) {
                    continue;
                }

                foreach ($reflectionAttributes as $reflectionAttribute) {
                    /** @var Route $attribute */
                    $attribute = $reflectionAttribute->newInstance();

                    Console::info(sprintf('Registering %s %s', $attribute->getHttpMethod()->name, $attribute->getUri()));
                    $r->addRoute($attribute->getHttpMethod()->name, $attribute->getUri(), $reflectionClass->getName());
                }
            }
        }, [
            'cacheFile' => $cacheFile,
        ]);
    }

    public function dispatch(HttpMethod $httpMethod, string $uri): array
    {
        Console::info(sprintf('Dispatching %s %s', $httpMethod->name, $uri));
        return $this->dispatcher->dispatch($httpMethod->name, $uri);
    }

    /**
     * @return class-string<HandlerInterface>[]
     */
    private function getHandlers(): array
    {
        $path = PROJECT_PATH . DIRECTORY_SEPARATOR;
        $handlerPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $this->config->get('handlers'));

        return array_map(
            fn(string $className): string => str_replace(DIRECTORY_SEPARATOR, '\\', $handlerPath . '\\' . $className),
            $this->fileSystemService->readDirectoryRecursive(
                $path . $handlerPath,
                filterFileType: '.php',
                relativePath: true,
                withoutFileType: true,
            )
        );
    }
}
