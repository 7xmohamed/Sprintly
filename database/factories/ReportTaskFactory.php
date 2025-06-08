<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\DailyReport;
use App\Models\ReportTask;
use App\Models\Task;

class ReportTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportTask::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'report_id' => DailyReport::factory(),
            'task_id' => Task::factory(),
            'hours_spent' => fake()->randomFloat(2, 0, 999.99),
            'progress_notes' => fake()->text(),
        ];
    }
}
