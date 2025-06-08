<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RepetitiveTask;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;

class BalancedRepetitiveTaskSeeder extends Seeder
{
    public function run()
    {
        // First clear existing repetitive tasks to avoid duplicates
        RepetitiveTask::truncate();
        
        // Get all tasks and make only some of them repetitive (about 30%)
        $tasks = Task::inRandomOrder()->get();
        $totalTasks = $tasks->count();
        $repetitiveCount = ceil($totalTasks * 0.3); // 30% of tasks will be repetitive
        
        // Take only a subset of tasks to make repetitive
        $tasksToMakeRepetitive = $tasks->take($repetitiveCount);
        
        foreach ($tasksToMakeRepetitive as $index => $task) {
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
        
        echo "Created {$repetitiveCount} repetitive tasks out of {$totalTasks} total tasks\n";
    }

    private function getRepetitionRate($index)
    {
        $rates = ["daily", "weekly", "monthly", "yearly"];
        return $rates[$index % count($rates)];
    }

    private function getRecurrenceDays($index)
    {
        // For weekly tasks - which days of week (bitwise: 1=Mon, 2=Tue, 4=Wed, 8=Thu, 16=Fri, 32=Sat, 64=Sun)
        $days = [0, 5, 0, 0, 31, 3]; // Various combinations: 5=Mon+Wed, 31=Mon-Fri, 3=Mon+Tue
        return $days[$index % count($days)];
    }

    private function getMonthDay($index)
    {
        // For monthly tasks - which day of month (1-31)
        $days = [0, 0, 15, 0, 1, 30]; // 1st, 15th and 30th of month
        return $days[$index % count($days)];
    }
} 