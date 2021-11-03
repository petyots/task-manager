<?php

namespace App\Domain\Users\Http\Requests\Auth;

use App\Interfaces\Http\Controllers\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $alphaRule = 'regex:/[А-Яа-яA-Za-z]/u';

        return [
            'first_name' => ['required', $alphaRule, 'max:255'],
            'last_name' => ['required', $alphaRule, 'max:255'],
            'password' => ['required', $this->passwordRule(), 'confirmed'],
            'email' => ['required', 'email', 'unique:users']
        ];
    }

    /**
     * Configure Password Rule
     *
     * @return Password
     */
    private function passwordRule(): Password
    {
        Password::defaults(function () {
            $rule = Password::min(8);

            return config('app.env') === 'production' ?
                $rule->mixedCase()->numbers()->symbols() :
                $rule;
        });

        return Password::default();
    }
}
