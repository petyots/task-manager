<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Tasks\DataTransferObjects\TaskData;
use App\Domain\Tasks\Models\Task;
use Illuminate\Support\Str;

class CreateOrUpdateTaskAction
{
    public function execute(TaskData $taskData): Task
    {
        // Convenient for PATCH method and POST at the same time
        // 1. Collect the properties
        // 2. Filter all non-null values
        // 3. Rename keys, so they are compatible to DB snake case format
        $props = collect($taskData->toArray())
            ->filter(fn($val) => $val !== null)
            ->keyBy(fn($val, $key) => Str::snake($key))
            ->toArray();

        $task = Task::firstOrNew([
            'uuid' => $taskData->uuid,
        ], $props);

        $task->saveOrFail();

        return $task;
    }
}
