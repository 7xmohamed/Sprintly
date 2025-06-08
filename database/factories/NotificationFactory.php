<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\User;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'message' => fake()->text(),
            'is_read' => fake()->boolean(),
            'created_at' => fake()->dateTime(),
            'from_id' => User::factory(),
            'type' => fake()->randomElement(["assignment","reminder","status_change","mention","approval"]),
        ];
    }
}
