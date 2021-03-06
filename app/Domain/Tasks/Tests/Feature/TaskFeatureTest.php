<?php

namespace App\Domain\Tasks\Tests\Feature;

use App\Domain\Tasks\Enums\TaskStatusEnum;
use App\Domain\Tasks\Events\NewTaskCreated;
use App\Domain\Tasks\Events\TaskUpdated;
use App\Domain\Tasks\Models\Task;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUser = User::factory()->create([
            'uuid' => Str::uuid()->toString(),
            'email' => 'test@test.com',
        ]);
    }

    public function testCanCreateTask()
    {
        $resp = $this->actingAs($this->loginUser)
            ->post(route('api.task.store'), [
                'name' => 'Test Task',
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertSee('Task created.');

        $this->assertDatabaseHas('tasks',
            [
                'uuid' => $resp->json('data.uuid'),
                'name' => 'Test Task'
            ]);
    }

    public function testTaskCreatedEventDispatched()
    {
        Event::fake();

        $this->actingAs($this->loginUser)
            ->post(route('api.task.store'), [
                'name' => 'Test Task',
            ]);

        Event::assertDispatched(NewTaskCreated::class);
    }

    public function testCantCreateTaskBecauseTheTitleContainsNotAllowedSymbols()
    {
        $this->actingAs($this->loginUser)
            ->post(route('api.task.store'), [
                'name' => 'Test Task {{{}'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name'], 'data.errors');
    }

    public function testCanChangeTaskStatus()
    {
        $task = Task::factory()->for($this->loginUser)->create();

        $this->actingAs($this->loginUser)
            ->post(route('api.task.change_status', $task->uuid), [
                'status' => TaskStatusEnum::DONE->name
            ])
            ->assertStatus(200)
            ->assertSee('Task status updated successfully.');

        $this->assertDatabaseHas('tasks', ['uuid' => $task->uuid, 'status' => TaskStatusEnum::DONE->value]);
    }

    public function testTaskUpdatedEventDispatched()
    {
        Event::fake();

        $task = Task::factory()->for($this->loginUser)->create();

        $this->actingAs($this->loginUser)
            ->post(route('api.task.change_status', $task->uuid), [
                'status' => TaskStatusEnum::DONE->name
            ])
            ->assertStatus(200)
            ->assertSee('Task status updated successfully.');

        Event::assertDispatched(TaskUpdated::class);
    }

    public function testCantChangeTaskStatusToCurrentStatus()
    {
        $task = Task::factory()
            ->done()
            ->for($this->loginUser)
            ->create([
                'name' => 'Test Task',
            ]);

        $this->actingAs($this->loginUser)
            ->post(route('api.task.change_status', $task->uuid), [
                'status' => TaskStatusEnum::DONE->name
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['status' => 'Status DONE is already set for this task.'], 'data.errors');

    }

    public function testCantChangeTaskStatusBecauseTheTaskDoesNotBelongToTheLoggedUser()
    {
        $task = Task::factory()
            ->for(User::factory()->create(['uuid' => Str::uuid()->toString(), 'email' => 'a@b.c']))
            ->create([
                'name' => 'Test Task',
            ]);

        $this->actingAs($this->loginUser)
            ->post(route('api.task.change_status', $task->uuid), [
                'status' => TaskStatusEnum::DONE->name
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertSee('This action is unauthorized.');
    }

    public function testCanSeeOwnTasks()
    {
        Task::factory()
            ->for($this->loginUser)
            ->randomStatus()
            ->count(10)
            ->create();

        $this->actingAs($this->loginUser)
            ->get(route('api.task.index'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data.tasks');
    }

    public function testCantSeeOtherUserTasks()
    {
         Task::factory()
            ->for(User::factory()->create(['uuid' => Str::uuid()->toString(), 'email' => 'a@b.c']))
            ->create([
                'name' => 'Test Task',
            ]);

        $this->actingAs($this->loginUser)
            ->get(route('api.task.index'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(0, 'data.tasks');
    }
}
