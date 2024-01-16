<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\Console;

final class Console
{
    private function __construct()
    {
    }

    public static function info(string $message): void
    {
        self::write(OutputType::INFO, $message);
    }

    public static function debug(string $message): void
    {
        self::write(OutputType::DEBUG, $message);
    }

    public static function error(string $message): void
    {
        self::write(OutputType::ERROR, $message);
    }

    public static function critical(string $message): void
    {
        self::write(OutputType::CRITICAL, $message);
    }

    public static function success(string $message): void
    {
        self::write(OutputType::SUCCESS, $message);
    }

    public static function dump(mixed $o): void
    {
        self::write(OutputType::DUMP, print_r($o, true));
    }

    private static function write(OutputType $outputType, string $message): void
    {
        echo $outputType->formatText(sprintf('[%s] %s', $outputType->name, $message) . PHP_EOL);
    }
}
