<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Routing;

enum HttpMethod
{
    case POST;
    case GET;
    case OPTION;
    case PUT;
    case PATCH;
    case DELETE;

    public static function fromIgnoreCase(string $method): self
    {
        foreach (self::cases() as $case) {
            if ($case->name === strtoupper($method)) {
                return $case;
            }
        }

        throw new \InvalidArgumentException(sprintf('No matching result found for %s', $method));
    }
}
