<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\EmailReminder;
use App\Models\Task;
use App\Models\User;

class EmailReminderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailReminder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'time' => fake()->dateTime(),
            'status' => fake()->randomElement(["pending","sent","failed"]),
            'sent_at' => fake()->dateTime(),
        ];
    }
}
