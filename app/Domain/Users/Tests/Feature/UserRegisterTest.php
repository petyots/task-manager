<?php

namespace App\Domain\Users\Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    public function testCanRegister()
    {
        $this->postJson(route('api.auth.register'), [
            'name'                  => 'test',
            'email'                 => 'test@test.com',
            'password'              => 'secretxxx-test',
            'password_confirmation' => 'secretxxx-test',
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertSee('We sent a confirmation email to test@test.com');
    }
}
