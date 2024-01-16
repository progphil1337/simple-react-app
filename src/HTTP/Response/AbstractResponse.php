<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Response;

use React\Http\Message\Response as HttpResponse;

abstract class AbstractResponse implements ResponseInterface
{
    public function __construct(
        private readonly string $content,
        private readonly int    $code = 200,
        private array           $header = ['charset' => 'utf-8']
    )
    {
    }

    public function writeHeader(string $key, mixed $value): self
    {
        $this->header[$key] = $value;

        return $this;
    }

    abstract protected function getContentType(): string;

    public function toHttpResponse(): HttpResponse
    {
        if ($this->getContentType() !== null) {
            $this->writeHeader('Content-Type', $this->getContentType());
        }

        return new HttpResponse($this->code, $this->header, $this->content);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getHeader(): array
    {
        return $this->header;
    }
}
