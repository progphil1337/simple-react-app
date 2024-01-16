<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\FileSystem\Response;

use ProgPhil1337\SimpleReactApp\FileSystem\File;
use ProgPhil1337\SimpleReactApp\HTTP\Response\AbstractResponse;

class FileResponse extends AbstractResponse
{
    public function __construct(private readonly File $file, int $code = 200, array $header = ['charset' => 'utf-8'])
    {
        parent::__construct($this->file->getContents(), $code, $header);
    }

    protected function getContentType(): string
    {
        return $this->file->getMimeType()->getContentType();
    }
}
