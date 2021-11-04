<?php

namespace App\Domain\Tasks\Factories;

use App\Domain\Tasks\DataTransferObjects\TaskData;
use App\Domain\Tasks\Enums\TaskStatusEnum;
use App\Domain\Tasks\Http\Requests\ChangeTaskStatusRequests;
use App\Domain\Tasks\Http\Requests\CreateTaskRequest;
use Illuminate\Support\Str;

class TaskDataFactory
{
    public static function fromCreateTaskRequest(CreateTaskRequest $request): TaskData
    {
        return new TaskData(
            uuid: Str::uuid()->toString(),
            name: $request->get('name'),
            userId: auth()->id(),
            status: TaskStatusEnum::WAITING->value
        );
    }

    public static function updateTaskDataFromChangeStatsRequest(ChangeTaskStatusRequests $requests): TaskData
    {
        return new TaskData(
            uuid: $requests->route('task'),
            name: null,
            userId: null,
            status: TaskStatusEnum::from($requests->get('status'))->value
        );
    }
}
