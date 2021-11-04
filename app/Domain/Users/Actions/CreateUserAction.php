<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\DataTransferObjects\CreateUserData;
use App\Domain\Users\Models\User;

class CreateUserAction
{
    public function execute(CreateUserData $data): User
    {
        return User::create([
            'uuid' => $data->uuid,
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'email' => $data->email,
            'password' => $data->password
        ]);
    }
}
