<?php

namespace App\Domain\Tasks\Database\Factories;

use App\Domain\Tasks\Enums\TaskStatusEnum;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'name' => $this->faker->text(120),
            'status' => TaskStatusEnum::WAITING->value
        ];
    }

    public function done(): TaskFactory
    {
        return $this->state(function (array $attributes) {
            return ['status' => TaskStatusEnum::DONE->value];
        });
    }
}
