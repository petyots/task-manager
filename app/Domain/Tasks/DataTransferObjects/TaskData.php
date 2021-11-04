<?php

namespace App\Domain\Tasks\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class TaskData implements Arrayable
{
    public function __construct(
        public readonly string $uuid,
        public readonly ?string $name,
        public readonly ?int $userId,
        public ?int $status,
    ) {
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
