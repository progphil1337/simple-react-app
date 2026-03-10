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

---

### Interface binding example

If you type-hint an **interface** in your handler's or service's constructor , you must register two additional things in your `ClassLookup` bootstrap:

1. `->alias(Interface::class, Concrete::class)` — maps the interface to its concrete implementation
2. `->register($classLookup)` — registers the configured instance itself, so the injector doesn't create a blank one at request time

Without these, you will get the following runtime error when a request hits the endpoint:
```
Cannot instantiate interface App\Service\SomeInterface
```

> ⚠️ This error only appears at **request time**, not on startup — making it easy to miss.

#### app.php
```php
use Progphil1337\Config\Config;
use ProgPhil1337\DependencyInjection\ClassLookup;
use ProgPhil1337\DependencyInjection\Injector;
use ProgPhil1337\SimpleReactApp\App;
use ProgPhil1337\SimpleReactApp\FileSystem\Pipeline\FileSystemPipelineHandler;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\DefaultRequestPipelineHandler;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\RoutingPipelineHandler;
use App\Service\SomeInterface;
use App\Service\SomeConcreteClass;

require_once 'vendor/autoload.php';

const PROJECT_PATH = __DIR__;

$config = Config::create(__DIR__ . '/config.yml');

$classLookup = (new ClassLookup());
$classLookup
    ->singleton(ClassLookup::class)
    ->alias(SomeInterface::class, SomeConcreteClass::class)
    ->singleton($config)
    ->singleton(Injector::class)
    ->register($config)
    ->register($classLookup); // ← must be THIS instance, not a new one

$container = new Injector($classLookup);

$app = new App($config, $container);

return $app->run([
    FileSystemPipelineHandler::class,
    RoutingPipelineHandler::class,
    DefaultRequestPipelineHandler::class
]);
```

#### App/Handler/SomeHandler.php
```php
use App\Service\SomeInterface;

#[Route(HttpMethod::GET, '/some-route')]
class SomeHandler implements HandlerInterface
{
    public function __construct(
        private readonly SomeInterface $service // ← injected via alias
    ) {}

    public function process(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        return new JSONResponse(['result' => $this->service->doSomething()]);
    }
}
```