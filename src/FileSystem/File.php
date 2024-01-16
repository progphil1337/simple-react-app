<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\FileSystem;

class File
{
    private readonly MimeType $mimeType;

    public function __construct(
        private readonly string $systemPath
    )
    {
        $this->mimeType = MimeType::fromFile($this);
    }

    public function getSystemPath(): string
    {
        return $this->systemPath;
    }

    public function getMimeType(): MimeType
    {
        return $this->mimeType;
    }

    public function getContents(): string
    {
        return file_get_contents($this->getSystemPath());
    }
}
