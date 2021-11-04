<?php

namespace App\Domain\Tasks\Events;

use App\Domain\Tasks\Models\Task;

class TaskUpdated
{
    public function __construct(public readonly Task $task)
    {
    }
}
