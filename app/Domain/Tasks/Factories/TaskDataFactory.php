<?php

namespace App\Domain\Tasks\Factories;

use App\Domain\Tasks\DataTransferObjects\NewTaskData;
use App\Domain\Tasks\DataTransferObjects\UpdateTaskData;
use App\Domain\Tasks\Enums\TaskStatusEnum;
use App\Domain\Tasks\Http\Requests\ChangeTaskStatusRequests;
use App\Domain\Tasks\Http\Requests\CreateTaskRequest;
use Illuminate\Support\Str;

class TaskDataFactory
{
    public static function newTaskDataFromCreateTaskRequest(CreateTaskRequest $request): NewTaskData
    {
        return new NewTaskData(
            name: $request->get('name'),
            uuid: Str::uuid()->toString(),
            userId: auth()->id(),
            status: TaskStatusEnum::WAITING
        );
    }

    public static function updateTaskDataFromChangeStatsRequest(ChangeTaskStatusRequests $requests): UpdateTaskData
    {
        return new UpdateTaskData(
            task: $requests->route('task'),
            name: null,
            status: TaskStatusEnum::from($requests->get('status'))
        );
    }
}
