<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
            'project_id' => Project::factory(),
            'created_by' => User::factory(),
            'created_at' => fake()->dateTime(),
            'due_date' => fake()->date(),
            'priority' => fake()->randomElement(["low","medium","high"]),
            'current_status' => fake()->randomElement(["to_do","in_progress","completed"]),
            'start_date' => fake()->date(),
            'status' => fake()->randomElement(["pending_approval","approved"]),
        ];
    }
}
