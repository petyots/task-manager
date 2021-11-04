<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Tasks\DataTransferObjects\NewTaskData;
use App\Domain\Tasks\DataTransferObjects\UpdateTaskData;
use App\Domain\Tasks\Models\Task;

class CreateOrUpdateTaskAction
{
    public function execute(NewTaskData | UpdateTaskData $taskData): Task
    {
        if ($taskData instanceof UpdateTaskData) {
            $props =  array_filter((array) $taskData, fn($val, $key) => $val !== null);
            dd($props);
        }

        $task = Task::firstOrNew([
            'uuid' => $taskData->uuid,
        ], [
            'user_id' => $taskData->userId,
            'name' => $taskData->name,
            'status' => $taskData->status->value
        ]);

        $task->saveOrFail();

        return $task;
    }
}
