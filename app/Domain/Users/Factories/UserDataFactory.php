<?php

namespace App\Domain\Users\Factories;

use App\Domain\Users\DataTransferObjects\CreateUserData;
use App\Domain\Users\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserDataFactory
{
    public static function newCreateUserDataFromRegisterRequest(RegisterRequest $request): CreateUserData
    {
        return new CreateUserData(
            uuid: Str::uuid(),
            firstName: $request->get('first_name'),
            lastName: $request->get('last_name'),
            email: $request->get('email'),
            password: Hash::make($request->get('password'))
        );
    }
}
