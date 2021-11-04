<?php

namespace App\Domain\Tasks\Enums;

enum TaskStatusEnum: int
{
    case WAITING = 1;
    case DONE = 2;

    public static function tryFromName(string $name): ?TaskStatusEnum
    {
        return match ($name) {
            self::WAITING->name => self::WAITING,
            self::DONE->name => self::DONE,
            default => null
        };
    }
}
