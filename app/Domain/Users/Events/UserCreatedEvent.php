<?php

namespace App\Domain\Users\Events;

use App\Domain\Users\Models\User;

class UserCreatedEvent
{
    public function __construct(public readonly User $user)
    {
    }
}
