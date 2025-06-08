<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\RepetitiveTask;
use App\Models\TaskAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateTaskPage extends Component
{
    public $title = '';
    public $description = '';
    public $project_id = '';
    public $priority = 'medium';
    public $current_status = 'todo';
    public $start_date = '';
    public $due_date = '';
    public $status = 'pending_approval';
    public $assignees = [];
    public $projectMembers = [];
    
    // Repetitive task fields
    public $is_repetitive = false;
    public $repetition_rate = 'weekly';
    public $recurrence_days = [];
    public $recurrence_month_day = 1;
    public $recurrence_end_date = '';
    
    public $projects = [];

    protected $rules = [
        'title' => 'required|string|max:100',
        'description' => 'nullable|string',
        'project_id' => 'required|exists:projects,id',
        'due_date' => 'nullable|date|after_or_equal:today',
        'priority' => 'nullable|in:low,medium,high',
        'current_status' => 'nullable|in:todo,in_progress,completed',
        'start_date' => 'nullable|date|after_or_equal:today',
        'status' => 'nullable|in:pending_approval,approved',
        'assignees' => 'nullable|array',
        'assignees.*' => 'exists:users,id',
        'is_repetitive' => 'boolean',
        'repetition_rate' => 'required_if:is_repetitive,true|in:daily,weekly,monthly,yearly',
        'recurrence_days' => 'required_if:repetition_rate,weekly|array',
        'recurrence_month_day' => 'required_if:repetition_rate,monthly|integer|min:1|max:31',
        'recurrence_end_date' => 'nullable|date|after_or_equal:due_date',
    ];

    protected $messages = [
        'due_date.after_or_equal' => 'The due date must be today or a future date.',
        'start_date.after_or_equal' => 'The start date must be today or a future date.',
    ];

    protected function casts()
    {
        return [
            'is_repetitive' => 'boolean',
        ];
    }

    public function mount()
    {
        // Set default dates
        $today = Carbon::today()->format('Y-m-d');
        $this->start_date = $today;
        $this->due_date = $today;
        
        // Set default recurrence values
        $dayOfWeek = Carbon::today()->dayOfWeek;
        $this->recurrence_days = [$dayOfWeek];
        $this->recurrence_month_day = Carbon::today()->day;
        
        // If due_date is passed as a query parameter, set it
        if (request()->has('due_date')) {
            $this->due_date = request('due_date');
            $this->start_date = request('due_date');
            
            $dayOfWeek = Carbon::parse(request('due_date'))->dayOfWeek;
            $this->recurrence_days = [$dayOfWeek];
            $this->recurrence_month_day = Carbon::parse(request('due_date'))->day;
        }
        
        // Load projects where the user is a member
        $this->projects = Project::whereHas('members', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
    }

    public function updatedIsRepetitive($value)
    {
        $this->is_repetitive = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        if ($this->is_repetitive && empty($this->recurrence_days)) {
            $this->recurrence_days = [now()->dayOfWeek];
        }
    }

    public function updatedRepetitionRate($value)
    {
        if ($value === 'weekly' && empty($this->recurrence_days)) {
            $this->recurrence_days = [now()->dayOfWeek];
        } elseif ($value === 'monthly' && !$this->recurrence_month_day) {
            $this->recurrence_month_day = now()->day;
        }
    }

    public function updatedProjectId($value)
    {
        if ($value) {
            $this->loadProjectMembers($value);
        } else {
            $this->projectMembers = [];
        }
        $this->assignees = [];
    }

    protected function loadProjectMembers($projectId)
    {
        if ($projectId) {
            $project = Project::find($projectId);
            
            if ($project) {
                $this->projectMembers = $project->members()
                    ->select('users.*', 'project_members.role')
                    ->orderBy('project_members.role', 'desc')
                    ->get();
            }
        } else {
            $this->projectMembers = collect();
        }
    }

    private function calculateNextOccurrence()
    {
        $startDate = Carbon::parse($this->start_date);
        
        switch ($this->repetition_rate) {
            case 'daily':
                return $startDate->addDay();
            case 'weekly':
                // Find the next occurrence based on selected days
                $nextDate = $startDate->copy();
                do {
                    $nextDate->addDay();
                } while (!in_array($nextDate->dayOfWeek, $this->recurrence_days));
                return $nextDate;
            case 'monthly':
                return $startDate->addMonth()->setDay($this->recurrence_month_day);
            case 'yearly':
                return $startDate->addYear();
            default:
                return $startDate;
        }
    }

    public function create()
    {
        // Check if user has permission to create tasks
        if (!auth()->user()->hasPermissionTo('create tasks')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to create tasks.'
            ]);
            return;
        }

        $this->validate();
        
        // Start database transaction
        \DB::beginTransaction();

        try {
            // Create the task
            $task = Task::create([
                'title' => $this->title,
                'description' => $this->description,
                'project_id' => $this->project_id,
                'created_by' => Auth::id(),
                'due_date' => $this->due_date,
                'priority' => $this->priority,
                'current_status' => $this->current_status,
                'start_date' => $this->start_date,
                'status' => $this->status,
                'is_repetitive' => $this->is_repetitive,
            ]);
            
            // If task is repetitive, create the repetitive task record
            if ($this->is_repetitive) {
                // Calculate next occurrence based on repetition rate
                $nextOccurrence = $this->calculateNextOccurrence();
                
                // Create binary representation of days for weekly recurrence
                $daysBinary = 0;
                if ($this->repetition_rate === 'weekly' && !empty($this->recurrence_days)) {
                    foreach ($this->recurrence_days as $day) {
                        $daysBinary |= (1 << $day); // Set bit for each selected day
                    }
                }
                
                // Create the repetitive task record
                RepetitiveTask::create([
                    'task_id' => $task->id,
                    'project_id' => $this->project_id,
                    'created_by' => Auth::id(),
                    'repetition_rate' => $this->repetition_rate,
                    'recurrence_interval' => Carbon::now(),
                    'recurrence_days' => $daysBinary,
                    'recurrence_month_day' => $this->recurrence_month_day,
                    'start_date' => Carbon::parse($this->start_date),
                    'end_date' => $this->recurrence_end_date ? Carbon::parse($this->recurrence_end_date) : null,
                    'next_occurrence' => Carbon::parse($this->due_date),
                ]);
            }
            
            // Assign the task to selected users
            if (!empty($this->assignees)) {
                foreach ($this->assignees as $userId) {
                    TaskAssignment::create([
                        'task_id' => $task->id,
                        'user_id' => $userId,
                        'assigned_by' => Auth::id(),
                    ]);
                }
            }

            \DB::commit();

            // Show success notification
            $this->dispatch('notify', [
                'message' => 'Task created successfully!',
                'type' => 'success'
            ]);

            // Redirect to tasks page
            return redirect()->route('tasks.index');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            // Show error notification
            $this->dispatch('notify', [
                'message' => 'Error creating task: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tasks.create-task-page', [
            'weekdays' => [
                0 => 'Sunday',
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday'
            ]
        ]);
    }
} 