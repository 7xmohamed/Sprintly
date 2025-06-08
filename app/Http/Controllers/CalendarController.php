<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\RepetitiveTask;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the calendar view showing tasks from projects the user is part of.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        // Initialize tasks query with proper eager loading
        $tasksQuery = Task::with(['project', 'repetitiveTask', 'taskAssignments'])
            ->whereHas('taskAssignments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        
        // Apply role-based visibility rules
        if ($user->hasRole('director')) {
            // Directors can see all tasks
            $tasksQuery = Task::with(['project', 'repetitiveTask', 'taskAssignments']);
        } elseif ($user->hasRole('supervisor')) {
            // Supervisors can see all tasks in projects they supervise
            $tasksQuery = Task::with(['project', 'repetitiveTask', 'taskAssignments'])
                ->whereIn('project_id', function($query) use ($user) {
                    $query->select('id')
                        ->from('projects')
                        ->where('supervised_by', $user->id);
                });
        }
            
        // Get task assignments for the authenticated user
        $taskAssignments = TaskAssignment::where('user_id', $user->id)->get();
        $assignedTaskIds = $taskAssignments->pluck('task_id')->toArray();
        
        // Process tasks and generate repetitive instances
        $allTasks = collect();
        foreach ($tasksQuery->get() as $task) {
            // Mark if task is assigned to current user
            $task->assigned_to_user = in_array($task->id, $assignedTaskIds);
            
            // Add the base task
            $allTasks->push($task);
            
            // Generate repetitive instances if needed
            if ($task->repetitiveTask) {
                $task->is_repetitive = true;
                $task->repetition_rate = $task->repetitiveTask->repetition_rate;
                $instances = $this->generateRepetitiveInstances($task);
                $allTasks = $allTasks->concat($instances);
            } else {
                $task->is_repetitive = false;
            }
        }
        
        // Get projects for the filter dropdown based on role
        if ($user->hasRole('director')) {
            $projects = Project::all();
        } elseif ($user->hasRole('supervisor')) {
            $projects = Project::where('supervised_by', $user->id)->get();
        } else {
            $projects = Project::whereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        }
        
        return view('calendar.index', [
            'tasks' => $allTasks,
            'projects' => $projects,
            'assignedTaskIds' => $assignedTaskIds
        ]);
    }

    private function generateRepetitiveInstances($task)
    {
        $instances = collect();
        $repetitiveTask = $task->repetitiveTask;
        
        // Track dates we've already created instances for to prevent duplicates
        $processedDates = [];
        
        // Ensure we have valid dates
        try {
            $startDate = Carbon::parse($repetitiveTask->start_date);
            
            // For yearly tasks, we want to show at least 2 years worth of tasks
            $endDate = $repetitiveTask->end_date 
                ? Carbon::parse($repetitiveTask->end_date) 
                : ($repetitiveTask->repetition_rate === 'yearly' 
                    ? Carbon::now()->addYears(2) 
                    : Carbon::now()->addMonths(3));
            
            // If start date is in the future, don't generate instances yet
            if ($startDate->isFuture()) {
                return $instances;
            }
            
            // If end date is in the past, don't generate instances
            if ($endDate->isPast()) {
                return $instances;
            }
            
            // Adjust the range based on repetition type
            $oldestDate = $repetitiveTask->repetition_rate === 'yearly'
                ? Carbon::now()->subYear()
                : Carbon::now()->subMonths(3);
            
            $furthestDate = $repetitiveTask->repetition_rate === 'yearly'
                ? Carbon::now()->addYears(2)
                : Carbon::now()->addMonths(3);
            
            $startDate = $startDate->max($oldestDate);
            $endDate = $endDate->min($furthestDate);
            
            // Add the original task's date to processed dates to prevent duplication
            $processedDates[] = $task->start_date->format('Y-m-d');
            
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                $shouldCreateInstance = false;
                $nextDate = null;
                $dateKey = $currentDate->format('Y-m-d');
                
                // Skip if we already created an instance for this date
                if (in_array($dateKey, $processedDates)) {
                    $currentDate = $currentDate->copy()->addDay();
                    continue;
                }
                
                switch ($repetitiveTask->repetition_rate) {
                    case 'daily':
                        // For daily tasks, skip the start date as it's the original task
                        if ($currentDate->format('Y-m-d') !== $task->start_date->format('Y-m-d')) {
                            $shouldCreateInstance = true;
                        }
                        $nextDate = $currentDate->copy()->addDay();
                        break;
                        
                    case 'weekly':
                        // Check if the day is in recurrence_days using bitwise operation
                        $dayBit = 1 << $currentDate->dayOfWeek;
                        $shouldCreateInstance = ($repetitiveTask->recurrence_days & $dayBit) !== 0;
                        // Skip if it's the original task's date
                        if ($currentDate->format('Y-m-d') === $task->start_date->format('Y-m-d')) {
                            $shouldCreateInstance = false;
                        }
                        $nextDate = $currentDate->copy()->addDay();
                        break;
                        
                    case 'monthly':
                        // Only create instance on the specified day of the month
                        $shouldCreateInstance = $currentDate->day === $repetitiveTask->recurrence_month_day 
                            || ($currentDate->day === $currentDate->daysInMonth 
                                && $repetitiveTask->recurrence_month_day > $currentDate->daysInMonth);
                        // Skip if it's the original task's date
                        if ($currentDate->format('Y-m-d') === $task->start_date->format('Y-m-d')) {
                            $shouldCreateInstance = false;
                        }
                        // Move to the next month's recurrence day
                        $nextDate = $currentDate->copy()->addMonth()->setDay(1);
                        try {
                            $nextDate->setDay($repetitiveTask->recurrence_month_day);
                        } catch (\Exception $e) {
                            $nextDate->endOfMonth();
                        }
                        break;
                        
                    case 'yearly':
                        // Get the original start date's month and day
                        $originalStartDate = Carbon::parse($repetitiveTask->start_date);
                        
                        // Check if we're on the anniversary date
                        $shouldCreateInstance = $currentDate->month === $originalStartDate->month 
                            && $currentDate->day === $originalStartDate->day;
                        
                        // Skip if it's the original task's date
                        if ($currentDate->format('Y-m-d') === $task->start_date->format('Y-m-d')) {
                            $shouldCreateInstance = false;
                        }
                        
                        // Move to the next year's anniversary
                        if ($shouldCreateInstance) {
                            $nextDate = $currentDate->copy()->addYear();
                        } else {
                            // Move to the next occurrence
                            $nextDate = $currentDate->copy();
                            
                            // If we've passed this year's date, move to next year
                            if ($currentDate->month > $originalStartDate->month || 
                                ($currentDate->month === $originalStartDate->month && $currentDate->day > $originalStartDate->day)) {
                                $nextDate->addYear();
                            }
                            
                            $nextDate->month($originalStartDate->month)
                                   ->day($originalStartDate->day);
                        }
                        break;
                }
                
                if ($shouldCreateInstance) {
                    $instance = $this->createTaskInstance($task, $currentDate);
                    $instances->push($instance);
                    $processedDates[] = $dateKey;
                }
                
                $currentDate = $nextDate;
            }
            
        } catch (\Exception $e) {
            // Log the error but don't break the calendar
            \Log::error('Error generating repetitive instances for task ' . $task->id . ': ' . $e->getMessage());
        }
        
        return $instances;
    }

    private function createTaskInstance($task, $date)
    {
        $instance = clone $task;
        $instance->display_date = $date->copy();
        
        // Handle due dates for repetitive tasks
        if ($task->repetitiveTask) {
            $originalStartDate = Carbon::parse($task->repetitiveTask->start_date);
            $originalDueDate = Carbon::parse($task->due_date);
            
            // Calculate the offset in days between start and due date
            $daysOffset = $originalStartDate->diffInDays($originalDueDate, false);
            
            // Set the due date relative to the instance date
            $instance->due_date = $date->copy()->addDays($daysOffset);
            
            // Keep the original time
            $instance->due_date->setTime(
                $originalDueDate->hour,
                $originalDueDate->minute,
                $originalDueDate->second
            );
        } else {
            $instance->due_date = $date->copy();
        }
        
        $instance->is_repetitive_instance = true;
        $instance->original_task_id = $task->id;
        $instance->instance_date = $date->format('Y-m-d');
        $instance->is_past = $date->isPast();
        return $instance;
    }
} 
