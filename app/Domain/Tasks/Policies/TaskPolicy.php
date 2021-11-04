<?php

namespace App\Domain\Tasks\Policies;

use App\Domain\Tasks\Models\Task;
use Illuminate\Contracts\Auth\Authenticatable;

class TaskPolicy
{
    public function changeStatus(Authenticatable $authenticatable, Task $task): bool
    {
        return $task->user_id = $authenticatable->getAuthIdentifier();
    }
}
