<?php

namespace App\Domain\Users\DataTransferObjects;

class CreateUserData
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $password
    ) {
    }
}
