<?php

namespace Tests;

use App\Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

trait AttachJwtToken
{

    protected User $loginUser;

    /**
     * @param  Authenticatable  $user
     * @param  null  $driver
     *
     * @return $this
     */
    public function actingAs(Authenticatable $user, $driver = null)
    {
        $token = JWTAuth::fromUser($user);

        $this->withHeader('Authorization', 'Bearer '.$token);

        return $this;
    }
}
