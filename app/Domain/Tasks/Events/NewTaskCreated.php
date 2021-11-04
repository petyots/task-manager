<?php

namespace App\Domain\Tasks\Events;

use App\Domain\Tasks\Models\Task;

class NewTaskCreated
{
    public function __construct(public readonly Task $task)
    {
    }
}
