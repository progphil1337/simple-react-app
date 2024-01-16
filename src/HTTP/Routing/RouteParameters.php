<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Routing;

class RouteParameters
{
    public function __construct(
        private readonly array $parameters
    )
    {

    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }
}
