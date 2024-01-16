<?php

declare(strict_types=1);

namespace ProgPhil1337\SimpleReactApp\Console;

enum OutputType
{
    case INFO;
    case DEBUG;
    case ERROR;
    case CRITICAL;
    case SUCCESS;
    case DUMP;

    public function formatText(string $message): string
    {
        return str_replace('%s', $message, match ($this) {
            self::INFO => "\e[90m%s\e[39m",
            self::DEBUG => "\e[39m%s\e[39m",
            self::ERROR => "\e[91m%s\e[39m",
            self::CRITICAL => "\e[41m%s\e[39m",
            self::SUCCESS => "\e[32m%s\e[39m",
            self::DUMP => "\e[100m%s\e[49m",
        });
    }
}
