# Simple ReactPHP App
Simple and fast configuration solution

## Installation

Install with composer:
```bash
$ composer require progphil1337/simple-react-app
```

## Compatibility

`ProgPhil1337\SimpleReactApp` requires PHP 8.1 (or better).

## Usage


### Basic example
#### Run your app 
```bash
$ php app.php 
```
Example output
```shell
[INFO] Registering GET /
[INFO] Server running on 127.0.0.1:1337
```

#### app.php

```php
use Progphil1337\Config\Config;
use ProgPhil1337\DependencyInjection\ClassLookup;
use ProgPhil1337\DependencyInjection\Injector;
use ProgPhil1337\SimpleReactApp\App;
use ProgPhil1337\SimpleReactApp\FileSystem\Pipeline\FileSystemPipelineHandler;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\DefaultRequestPipelineHandler;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\RoutingPipelineHandler;

require_once 'vendor/autoload.php';

const PROJECT_PATH = __DIR__;

$config = Config::create(__DIR__ . '/config.yml');

$classLookup = (new ClassLookup())
    ->singleton($config)
    ->singleton(Injector::class)
    ->register($config);

$container = new Injector($classLookup);

$app = new App($config, $container);

return $app->run([
    FileSystemPipelineHandler::class,
    RoutingPipelineHandler::class,
    DefaultRequestPipelineHandler::class
]);
```

#### config.yml
```yml
host: 127.0.0.1
port: 1337
public_dir: public
routing:
  cache: route.cache
handlers: App/Handler
```

#### App/Handler/IndexHandler.php
```php 
#[Route(HttpMethod::GET, '/')]
class IndexHandler implements HandlerInterface
{
    public function process(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        return new JSONResponse(['message' => 'Hello World']);
    }
}
```