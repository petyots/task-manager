<?php

namespace App\Domain\Users\Tests\Unit;

use App\Domain\Users\DataTransferObjects\CreateUserData;
use App\Domain\Users\Factories\UserDataFactory;
use App\Domain\Users\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class UserDataTest extends TestCase
{
    private UserDataFactory $userDataFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userDataFactory = new UserDataFactory();
    }

    public function testUserDataFactoryHasCreateNewUserDataFromRegisterRequest()
    {
        $this->assertTrue(condition: method_exists(
            object_or_class: $this->userDataFactory,
            method: 'newCreateUserDataFromRegisterRequest'
        ));
    }

    public function testCreateNewUserDataHasRequiredProperties()
    {
        $this->assertClassHasAttribute('uuid', CreateUserData::class);
        $this->assertClassHasAttribute('firstName', CreateUserData::class);
        $this->assertClassHasAttribute('lastName', CreateUserData::class);
        $this->assertClassHasAttribute('email', CreateUserData::class);
        $this->assertClassHasAttribute('password', CreateUserData::class);

        $object = new CreateUserData(
            uuid: Str::uuid(),
            firstName: 'Test',
            lastName: 'Test',
            email: 'email@a.b',
            password: '123'
        );

        $this->assertEquals(CreateUserData::class, $object::class);
    }
}
