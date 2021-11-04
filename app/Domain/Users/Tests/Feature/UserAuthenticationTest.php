<?php

namespace App\Domain\Users\Tests\Feature;

use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUser = User::factory()->create([
            'uuid' => Str::uuid()->toString(),
            'email' => 'test@test.com',
        ]);
    }

    public function testCanLogIn()
    {
        $this->post(route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'secret!123'
        ])->assertStatus(Response::HTTP_OK);
    }

    public function testCantLoginInvalidEmailFormat()
    {
        $this->post(route('auth.login'), [
            'email' => 'test.test.com',
            'password' => 'secret!123'
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee('The email must be a valid email address.');
    }

    public function testCantLoginBecausePasswordIsTooShort()
    {
        $this->post(route('auth.login'), [
            'email' => 'test.test.com',
            'password' => 'pwd'
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee('The password must be at least 8 characters');
    }

    public function testCantLoginBecauseCredentialsAreInvalid()
    {
        $this->post(route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'password!234'
        ])->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertSee('Invalid Credentials.');
    }

    public function testCantLoginBecauseAlreadyLoggedIn()
    {

        $this->actingAs($this->loginUser)
            ->post(route('auth.login'), [
                'email' => 'test@test.com',
                'password' => 'secret!123'
            ])->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertSee('You are already logged in.');
    }

    public function testLoginTokenResponseContainsToken()
    {
        $resp = $this->post(route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'secret!123'
        ]);

        $this->assertArrayHasKey('access_token', $resp->json());
        $this->assertNotEmpty($resp->json('access_token'));
    }

    public function testLoginAccessTokenResponseExpiresInPropertyIsInTheFuture()
    {
        $resp = $this->post(route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'secret!123'
        ]);

        $expiresIn = $resp->json('expires_in');

        $this->assertTrue(now()->addSeconds($expiresIn)->gt(now()));
    }

    public function testLoginAccessTokenExpiresInByConfig()
    {

        $resp = $this->post(route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'secret!123'
        ]);

        $expiresIn = $resp->json('expires_in');

        $this->assertTrue($expiresIn === \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::factory()->getTTL() * 60);
    }

    public function testCanLogout()
    {
        $this->actingAs($this->loginUser)
            ->post(route('auth.logout'))
            ->assertStatus(Response::HTTP_OK)
            ->assertSee('You have successfully logged out.');
    }

    public function testCantLogoutBecauseNoTokenIsPresent()
    {
        $this->post(route('auth.logout'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertSee('Unauthenticated.');

    }

    public function testCantLogoutBecauseTokenIsInvalid()
    {
        $this->withHeaders(['Authorization' => 'Bearer InvalidToken'])
            ->post(route('auth.logout'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertSee('Unauthenticated.');
    }

    public function testCanRefreshToken()
    {

        $resp = $this->actingAs($this->loginUser)
            ->post(route('auth.refresh_token'));

        $resp->assertStatus(200);
        $this->assertArrayHasKey('access_token', $resp->json());
        $this->assertNotEmpty($resp->json('access_token'));
    }

    public function testCantRefreshTokenBecauseTokenIsAlreadyExpired()
    {
        Config::set('jwt.ttl', 1);
        Config::set('jwt.refresh_ttl', 0);


        $this->actingAs($this->loginUser)
            ->post(route('auth.refresh_token'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertSee('Token has expired');
    }

    public function testCantRefreshTokenBecauseTokenIsNotPresent()
    {
        $this->post(route('auth.logout'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertSee('Unauthenticated.');
    }

    public function testCanSeeOwnProfile()
    {
        $this->actingAs($this->loginUser)
            ->get(route('auth.profile'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.email', $this->loginUser->email)
            ->assertJsonPath('data.uuid', $this->loginUser->uuid);
    }

    public function testCantSeeOwnProfileBecauseNoTokenIsProvider()
    {
        $this->get(route('auth.profile'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertSee('Unauthenticated.');
    }
}
