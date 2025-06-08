<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'email' => fake()->safeEmail(),
            'password' => fake()->password(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'created_at' => fake()->dateTime(),
            'last_login' => fake()->dateTime(),
            'is_active' => fake()->boolean(),
            'role' => fake()->randomElement(["director", "supervisor", "team_leader", "employee"]),
        ];
    }
}
