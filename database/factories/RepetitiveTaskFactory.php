<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\RepetitiveTask;

class RepetitiveTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RepetitiveTask::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'task_id' => fake()->numberBetween(-100000, 100000),
            'project_id' => fake()->numberBetween(-100000, 100000),
            'created_by' => fake()->numberBetween(-100000, 100000),
            'repetition_rate' => fake()->randomElement(["daily","weekly","monthly","yearly"]),
            'recurrence_interval' => fake()->dateTime(),
            'recurrence_days' => fake()->numberBetween(-100000, 100000),
            'recurrence_month_day' => fake()->numberBetween(-100000, 100000),
            'start_date' => fake()->numberBetween(-100000, 100000),
            'end_date' => fake()->numberBetween(-100000, 100000),
            'next_occurrence' => fake()->numberBetween(-100000, 100000),
        ];
    }
}
