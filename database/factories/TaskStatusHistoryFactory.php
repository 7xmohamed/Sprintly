<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Models\User;

class TaskStatusHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskStatusHistory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'old_status' => fake()->regexify('[A-Za-z0-9]{50}'),
            'new_status' => fake()->regexify('[A-Za-z0-9]{50}'),
            'changed_at' => fake()->dateTime(),
            'notes' => fake()->text(),
        ];
    }
}
