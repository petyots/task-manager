<?php

namespace App\Domain\Users\Http\Requests\Auth;

use App\Interfaces\Http\Controllers\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', Password::default()]
        ];
    }
}
