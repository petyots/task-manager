<?php

namespace App\Domain\Tasks\Http\Requests;

use App\Interfaces\Http\Controllers\FormRequest;

class CreateTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'regex:/^[a-zA-Z0-9а-яА-Я!?.,;\s]+$/u', 'max:255']
        ];
    }
}
