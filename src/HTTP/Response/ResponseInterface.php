<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Response;

use React\Http\Message\Response as HttpResponse;

interface ResponseInterface
{
    public function writeHeader(string $key, mixed $value): self;

    public function toHttpResponse(): HttpResponse;

    public function getContent(): string;

    public function getCode(): int;

    /**
     * @return array<string, string>
     */
    public function getHeader(): array;
}
