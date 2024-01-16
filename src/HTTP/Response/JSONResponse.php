<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Response;

class JSONResponse extends AbstractResponse
{
    public function __construct(
        array $content,
        int   $code = 200,
        array $header = ['charset' => 'utf-8']
    )
    {
        parent::__construct(json_encode($content), $code, $header);
    }

    protected function getContentType(): string
    {
        return 'application/json';
    }
}
