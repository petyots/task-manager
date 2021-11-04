<?php

namespace App\Domain\Users\Tests\Feature;

use App\Domain\Users\Events\UserCreatedEvent;
use App\Domain\Users\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    public function testCanRegister()
    {
        $this->postJson(route('auth.register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'top_secret',
            'password_confirmation' => 'top_secret',
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertSee('User created.');

        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);

    }

    public function testUserCreatedEventDispatched()
    {
        Event::fake();

        $this->postJson(route('auth.register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'top_secret',
            'password_confirmation' => 'top_secret',
        ]);

        Event::assertDispatched(UserCreatedEvent::class);
    }

    public function testCannotRegisterBecausePasswordIsTooShort()
    {
        $this->postJson(route('auth.register'), [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee('The password must be at least 8 characters');
    }

    public function testCannotRegisterBecausePasswordsNotMatch()
    {
        $this->postJson(route('auth.register'), [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'secretxxx1',
            'password_confirmation' => 'secretxxx2',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee('The password confirmation does not match');
    }

    public function testCannotRegisterBecauseEmailAlreadyRegistered()
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('secretxxx'),
            'uuid' => Str::uuid()
        ]);

        $this->postJson(route('auth.register'), [
            'email' => 'test@test.com',
            'password' => 'secretxxx-test',
            'password_confirmation' => 'secretxxx-test',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee('email has already been taken');
    }
}
