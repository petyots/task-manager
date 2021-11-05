<?php

namespace App\Domain\Tasks\Tests\Unit;

use App\Domain\Tasks\Models\Task;
use App\Domain\Users\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    public function testTaskHasUser()
    {
        $user = User::factory(['uuid' => Str::uuid()->toString()]);
        $task = Task::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $task->user);
    }

    public function testTaskRouteKeyIsUuid()
    {
        $user = User::factory(['uuid' => Str::uuid()->toString()]);

        $task = Task::factory()->for($user)->create();

        $this->assertTrue($task->getRouteKeyName() === 'uuid');
    }
}
