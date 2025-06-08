<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RepetitiveTask;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;

class RepetitiveTaskSeeder extends Seeder
{
    public function run()
    {
        // Get existing tasks to make repetitive
        $tasks = Task::take(10)->get();
        $projects = Project::all();

        foreach ($tasks as $index => $task) {
            // Make the task repetitive
            RepetitiveTask::create([
                'task_id' => $task->id,
                'project_id' => $task->project_id,
                'created_by' => $task->created_by,
                'repetition_rate' => $this->getRepetitionRate($index),
                'recurrence_interval' => Carbon::now(),
                'recurrence_days' => $this->getRecurrenceDays($index),
                'recurrence_month_day' => $this->getMonthDay($index),
                'start_date' => strtotime(Carbon::now()->subDays(5)),
                'end_date' => strtotime(Carbon::now()->addMonths(3)),
                'next_occurrence' => strtotime(Carbon::now()->addDays(2)),
            ]);
        }
    }

    private function getRepetitionRate($index)
    {
        $rates = ["daily", "weekly", "monthly", "yearly", "daily", "weekly", "monthly", "weekly", "daily", "monthly"];
        return $rates[$index % count($rates)];
    }

    private function getRecurrenceDays($index)
    {
        // For weekly tasks - which days of week (bitwise: 1=Mon, 2=Tue, 4=Wed, 8=Thu, 16=Fri, 32=Sat, 64=Sun)
        $days = [0, 5, 0, 0, 0, 31, 0, 3, 0, 0]; // Various combinations: 5=Mon+Wed, 31=Mon-Fri, 3=Mon+Tue
        return $days[$index % count($days)];
    }

    private function getMonthDay($index)
    {
        // For monthly tasks - which day of month (1-31)
        $days = [0, 0, 15, 0, 0, 0, 1, 0, 0, 30]; // 1st, 15th and 30th of month
        return $days[$index % count($days)];
    }
} 