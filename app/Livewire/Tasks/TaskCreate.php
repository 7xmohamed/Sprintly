<?php

namespace App\Livewire\Tasks;

use App\Models\Project;
use App\Models\RepetitiveTask;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Notification;

class TaskCreate extends Component
{
    public $title = '';
    public $description = '';
    public $project_id = '';
    public $due_date = '';
    public $priority = 'medium';
    public $current_status = 'todo';
    public $start_date = '';
    public $status = 'pending_approval';
    public $assignees = [];
    public $projectMembers = [];
    
    // New repetitive task fields
    public $is_repetitive = false;
    public $repetition_rate = 'weekly';
    public $recurrence_days = []; 
    public $recurrence_month_day = 1; 
    public $recurrence_end_date = ''; 

    // Listen for project selection changes
    protected $listeners = ['projectSelected' => 'loadProjectMembers'];

    // Add property type casting
    protected function casts()
    {
        return [
            'is_repetitive' => 'boolean',
        ];
    }

    public function updatedIsRepetitive($value)
    {
        $this->is_repetitive = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        if ($this->is_repetitive) {
            // For repetitive tasks, initially set due_date same as start_date
            $this->due_date = $this->start_date;
            if (empty($this->recurrence_days)) {
                $this->recurrence_days = [now()->dayOfWeek];
            }
        }
    }

    public function updatedStartDate($value)
    {
        if ($this->is_repetitive) {
            // For repetitive tasks, keep due_date synchronized with start_date
            $this->due_date = $value;
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
    
    protected function rules()
    {
        return [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'due_date' => ['nullable', 'date', function ($attribute, $value, $fail) {
                if ($value && $this->start_date && Carbon::parse($value)->lt(Carbon::parse($this->start_date))) {
                    $fail('The due date must be after or equal to the start date.');
                }
            }],
            'priority' => 'nullable|in:low,medium,high',
            'current_status' => 'nullable|in:todo,in_progress,completed',
            'start_date' => 'nullable|date',
            'status' => 'nullable|in:pending_approval,approved',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'is_repetitive' => 'boolean',
            'repetition_rate' => 'required_if:is_repetitive,true|in:daily,weekly,monthly,yearly',
            'recurrence_days' => [
                'array',
                function ($attribute, $value, $fail) {
                    if ($this->is_repetitive && $this->repetition_rate === 'weekly' && empty($value)) {
                        $fail('The recurrence days field is required for weekly repetition.');
                    }
                }
            ],
            'recurrence_month_day' => [
                'integer',
                'min:1',
                'max:31',
                function ($attribute, $value, $fail) {
                    if ($this->is_repetitive && $this->repetition_rate === 'monthly' && empty($value)) {
                        $fail('The day of month is required for monthly repetition.');
                    }
                }
            ],
            'recurrence_end_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && $this->start_date && Carbon::parse($value)->lt(Carbon::parse($this->start_date))) {
                        $fail('The recurrence end date must be after or equal to the start date.');
                    }
                }
            ],
        ];
    }

    // Add custom validation messages
    protected $messages = [
        'due_date.after_or_equal' => 'The due date must be today or a future date.',
        'start_date.after_or_equal' => 'The start date must be today or a future date.',
    ];
    
    public function loadProjectMembers()
    {
        if ($this->project_id) {
            $project = Project::find($this->project_id);
            
            if ($project) {
                $this->projectMembers = $project->members()
                    ->select('users.*', 'project_members.role')
                    ->orderBy('project_members.role', 'desc')
                    ->get();
            }
        } else {
            $this->projectMembers = collect();
        }
        $this->assignees = [];
    }

    public function updatedProjectId($value)
    {
        logger()->info('Project ID Updated', ['new_value' => $value]);
        $this->loadProjectMembers();
    }

    public function mount($due_date = null)
    {
        // If no due_date was passed as a parameter, check the URL query string
        if (!$due_date) {
            $due_date = request()->query('due_date');
        }
        
        // Set default dates
        $defaultDate = $due_date ?? now()->format('Y-m-d');
        $this->start_date = $defaultDate;
        $this->due_date = $defaultDate;
        
        // If it's a monthly task, set the recurrence day to match the due date
        if ($due_date) {
            $this->recurrence_month_day = Carbon::parse($due_date)->day;
        }
        
        // Load project members if a project is already selected
        if ($this->project_id) {
            $this->loadProjectMembers();
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
            $this->dispatch('closeModal');
            return;
        }

        // Additional check for supervisors
        if (auth()->user()->hasRole('supervisor')) {
            $project = Project::find($this->project_id);
            if (!$project || $project->supervised_by !== auth()->id()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'You can only create tasks for projects you supervise.'
                ]);
                $this->dispatch('closeModal');
                return;
            }
        }

        $this->validate();
        
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
            // Create binary representation of days for weekly recurrence
            $daysBinary = 0;
            if ($this->repetition_rate === 'weekly' && !empty($this->recurrence_days)) {
                foreach ($this->recurrence_days as $day) {
                    $daysBinary |= (1 << $day); // Set bit for each selected day
                }
            }
            
            // Calculate the first occurrence date based on repetition rate
            $startDate = Carbon::parse($this->start_date);
            $nextOccurrence = null;
            
            switch ($this->repetition_rate) {
                case 'daily':
                    $nextOccurrence = $startDate->copy()->addDay();
                    break;
                case 'weekly':
                    $nextOccurrence = $startDate->copy()->addDay();
                    // Find the next day that matches our recurrence pattern
                    while (!($daysBinary & (1 << $nextOccurrence->dayOfWeek))) {
                        $nextOccurrence->addDay();
                    }
                    break;
                case 'monthly':
                    $nextOccurrence = $startDate->copy();
                    // If we haven't passed the recurrence day this month, use it
                    if ($startDate->day < $this->recurrence_month_day) {
                        $day = min((int)$this->recurrence_month_day, $startDate->daysInMonth);
                        $nextOccurrence->setDay($day);
                    } else {
                        // Otherwise, go to next month
                        $nextOccurrence->addMonth();
                        $day = min((int)$this->recurrence_month_day, $nextOccurrence->daysInMonth);
                        $nextOccurrence->setDay($day);
                    }
                    break;
                case 'yearly':
                    $nextOccurrence = $startDate->copy()->addYear();
                    break;
                default:
                    $nextOccurrence = $startDate->copy()->addDay();
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
                'next_occurrence' => $nextOccurrence,
            ]);
        }
        
        // Assign the task to selected users
        if (!empty($this->assignees)) {
            // Get all valid users in one query
            $validUsers = User::whereIn('id', $this->assignees)->pluck('id')->toArray();
            
            foreach ($validUsers as $userId) {
                TaskAssignment::create([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'assigned_by' => Auth::id(),
                ]);

                // Create notification for valid user
                Notification::create([
                    'user_id' => $userId,
                    'from_id' => auth()->id(),
                    'title' => 'New Task Assignment',
                    'message' => 'You have been assigned to task: ' . $task->title,
                    'type' => 'assignment',
                    'data' => [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'project_id' => $this->project_id,
                        'assigned_by' => auth()->user()->name
                    ],
                    'is_read' => false
                ]);
            }

            // If some users were invalid, show a warning
            if (count($validUsers) < count($this->assignees)) {
                $this->dispatch('notify', [
                    'message' => 'Some selected users could not be assigned to the task.',
                    'type' => 'warning',
                ]);
            }
        }
        
        $this->resetForm();
        $this->dispatch('taskCreated');
        $this->dispatch('closeModal');

        $this->dispatch('notify', [
            'message' => 'Task created successfully!',
            'type' => 'success',
        ]);
    }
    
    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->project_id = '';
        $this->due_date = '';
        $this->priority = 'medium';
        $this->current_status = 'todo';
        $this->start_date = '';
        $this->status = 'pending_approval';
        $this->assignees = [];
        $this->is_repetitive = false;
        $this->repetition_rate = 'weekly';
        $this->recurrence_days = [];
        $this->recurrence_month_day = 1;
        $this->recurrence_end_date = '';
    }
    
    public function render()
    {
        $user = auth()->user();
        
        // Get projects based on user role
        if ($user->hasRole('director')) {
            $projects = Project::all();
        } elseif ($user->hasRole('supervisor')) {
            $projects = Project::where('supervised_by', $user->id)->get();
        } else {
            $projects = Project::whereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        }

        return view('livewire.tasks.task-create', [
            'projects' => $projects,
            'projectMembers' => $this->projectMembers,
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