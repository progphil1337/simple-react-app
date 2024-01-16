<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\HTTP\Response;

class HTMLResponse extends AbstractResponse
{
    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return 'text/html';
    }
}
