<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\User;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'created_by' => User::factory(),
            'created_at' => fake()->dateTime(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'supervised_by' => User::factory(),
            'status' => fake()->randomElement(["planning","in_progress","completed","on_hold"]),
        ];
    }
}
