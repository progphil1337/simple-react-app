<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute;

use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;

#[\Attribute(flags: \Attribute::TARGET_CLASS)]
final class Route
{
    public function __construct(
        private readonly HttpMethod $httpMethod,
        private readonly string     $uri,
    )
    {
    }

    public function getHttpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
