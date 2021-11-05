<?php

namespace App\Domain\Tasks\Http\Resources;

use App\Domain\Tasks\Enums\TaskStatusEnum;
use App\Domain\Tasks\Models\Task;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Task $task */
        $task = $this;

        return [
            'uuid' => $task->uuid,
            'name' => $task->name,
            'status' => TaskStatusEnum::tryFrom($task->status),
            'modified_at' => $task->updated_at->toDateTimeString()
        ];
    }
}
