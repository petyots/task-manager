<?php

namespace App\Domain\Tasks\Http\Requests;

use App\Domain\Tasks\Enums\TaskStatusEnum;
use App\Domain\Tasks\Models\Task;
use App\Interfaces\Http\Controllers\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class ChangeTaskStatusRequests extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'status' => TaskStatusEnum::tryFromName($this->get('status'))?->value
        ]);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(TaskStatusEnum::class)]
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $this->validateAgainstCurrentStatus($validator);
        });
    }

    private function validateAgainstCurrentStatus(Validator $validator): void
    {
        $task = Task::query()->where('uuid', '=', $this->route('task'))
            ->first(['status', 'uuid']);

        $status = TaskStatusEnum::tryFrom($this->get('status'));

        if ($task->status === $status) {
            $validator->errors()->add(key: 'status',
                message: __('Status :status is already set for this task.',
                replace: ['status' => $status->name])
            );
            $this->failedValidation($validator);
        }
    }
}
