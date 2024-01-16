<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\FileSystem;

use Progphil1337\Config\Config;
use ProgPhil1337\SimpleReactApp\FileSystem\Exception\UnservableFileException;

class FileSystemService
{
    private readonly string $scope;

    /** @var array<string, File> */
    private array $cache = [];

    public function __construct(private readonly Config $config)
    {
        $this->scope = PROJECT_PATH . DIRECTORY_SEPARATOR . $this->config->get('public_dir');
    }

    /**
     * @param string $path
     * @return File
     * @throws UnservableFileException
     */
    public function getFile(string $path): File
    {
        $path = $this->toSystemPath($this->config->get('public_dir') . DIRECTORY_SEPARATOR . $path);
        if (!$this->canServeFile($path)) {
            throw new UnservableFileException();
        }

        return $this->cache[$path] ??= new File($path);
    }

    private function canServeFile(string $path): bool
    {
        return $this->pathInScope($path) && file_exists($path) && !is_dir($path);
    }

    public function toSystemPath(string $path): string
    {
        $path = PROJECT_PATH . DIRECTORY_SEPARATOR . $path;
        $subs = explode(DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $path));

        $pos = count($subs) - 1;
        $rem = 0;
        $systemPath = [];
        do {
            $current = $subs[$pos--];
            if ($current === '..') {
                $rem++;
            } else {
                if ($rem > 0) {
                    $rem--;
                } else {
                    $systemPath[] = $current;
                }
            }
        } while ($pos >= 0);

        return implode(DIRECTORY_SEPARATOR, array_reverse($systemPath));
    }

    public function readDirectoryRecursive(
        string  $path,
        bool    $skipPrevious = true,
        bool    $skipCurrent = true,
        ?string $filterFileType = null,
        bool    $relativePath = false,
        bool    $withoutFileType = false,
    ): array
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('No directory provided: %s', $path));
        }

        $handle = opendir($path);
        if (!$handle) {
            throw new \RuntimeException(sprintf('Unable to get handle for directory: %s', $path));
        }

        $result = [];

        while (($file = readdir($handle)) !== false) {
            if (($file === '.' && $skipCurrent) || ($file === '..' && $skipPrevious)) {
                continue;
            }

            $currentPath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($currentPath)) {
                foreach (
                    $this->readDirectoryRecursive(
                        $currentPath,
                        $skipPrevious,
                        $skipCurrent,
                        $filterFileType,
                        $relativePath,
                        $withoutFileType
                    ) as $subPath) {
                    $result[] = ($relativePath ? $file . DIRECTORY_SEPARATOR : '') . $subPath;
                }
            } else {
                if ($filterFileType && !str_ends_with($currentPath, $filterFileType)) {
                    continue;
                }

                $value = $relativePath ? $file : $currentPath;

                $result[] = $withoutFileType ? substr($value, 0, strrpos($value, '.')) : $value;
            }
        }

        return $result;
    }

    private function pathInScope(string $path): bool
    {
        return str_starts_with($path, $this->scope);
    }

    public function clearDirectory(string $path): void
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('Directory doesnt exist: %s', $path));
        }

        foreach ($this->readDirectoryRecursive($path) as $file) {
            unlink($file);
        }
    }
}
