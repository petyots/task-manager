<?php

namespace App\Domain\Tasks\DataTransferObjects;

use App\Domain\Tasks\Enums\TaskStatusEnum;

class UpdateTaskData
{
    public function __construct(
        public readonly int|string $task,
        public readonly ?string $name,
        public readonly ?TaskStatusEnum $status,
    ) {
    }
}
