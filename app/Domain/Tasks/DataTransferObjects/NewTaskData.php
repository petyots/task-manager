<?php

namespace App\Domain\Tasks\DataTransferObjects;

use App\Domain\Tasks\Enums\TaskStatusEnum;

class NewTaskData
{
    public function __construct(
        public readonly string $name,
        public readonly string $uuid,
        public readonly int $userId,
        public TaskStatusEnum $status,
    ) {
    }
}
