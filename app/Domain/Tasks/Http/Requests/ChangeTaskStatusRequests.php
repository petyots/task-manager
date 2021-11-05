<?php

namespace App\Domain\Tasks\Http\Requests;

use App\Domain\Tasks\Enums\TaskStatusEnum;
use App\Domain\Tasks\Models\Task;
use App\Interfaces\Http\Controllers\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class ChangeTaskStatusRequests extends FormRequest
{
    private ?Task $task = null;

    protected function prepareForValidation()
    {
        $this->merge([
            'task' => $this->route('task'),
            'status' => TaskStatusEnum::tryFromName($this->get('status'))?->value
        ]);
    }

    public function authorize(): bool
    {
        $this->task = Task::query()
            ->where('uuid', '=', $this->route('task'))
            ->firstOrFail(['user_id', 'status']);

        return Gate::check(['changeStatus'], [$this->task]);
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
        $status = TaskStatusEnum::from($this->offsetGet('status'));

        if ($this->task->status === $status->value) {
            $validator->errors()->add(key: 'status',
                message: __('Status :status is already set for this task.',
                replace: ['status' => $status->name])
            );
            $this->failedValidation($validator);
        }
    }
}
