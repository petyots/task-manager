<?php

namespace App\Domain\Users\Tests\Unit;

use App\Domain\Tasks\Models\Task;
use App\Domain\Users\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function testUserHasTasks()
    {
        $user = User::factory(['uuid' => Str::uuid()->toString()])
            ->has(Task::factory()->count(3))
            ->create();


        $this->assertContainsOnlyInstancesOf(Task::class, $user->tasks);
    }
}
