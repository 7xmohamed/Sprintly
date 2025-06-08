<?php

namespace App\Livewire\Tasks;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    public $search = '';
    public $projectFilter = '';
    public $priorityFilter = '';
    public $statusFilter = '';
    public $assigneeFilter = '';
    public $repetitiveFilter = '';
    public $sortField = 'due_date';
    public $sortDirection = 'asc';
    public $perPage = 10;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'projectFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'assigneeFilter' => ['except' => ''],
        'repetitiveFilter' => ['except' => ''],
        'sortField' => ['except' => 'due_date'],
        'sortDirection' => ['except' => 'asc'],
    ];

    protected $listeners = ['taskUpdated' => '$refresh', 'taskCreated' => '$refresh', 'taskDeleted' => '$refresh'];

    public function mount()
    {
        // Check if there's a task ID in the query string to open
        $taskId = request()->query('open_task');
        $editTaskId = request()->query('edit_task');
        $newTask = request()->query('new_task');
        $dueDate = request()->query('due_date');
        
        if ($newTask) {
            // Dispatch an event to open the create modal after the component is rendered
            $this->dispatch('defer-load-task-create', ['due_date' => $dueDate]);
        } elseif ($editTaskId) {
            // Check if the task exists for editing
            $task = Task::find($editTaskId);
            if ($task) {
                // Dispatch an event to open the edit modal after the component is rendered
                $this->dispatch('defer-load-task-edit', $editTaskId);
            }
        } elseif ($taskId) {
            // Check if the task exists for viewing
            $task = Task::find($taskId);
            if ($task) {
                // Dispatch an event to open the view modal after the component is rendered
                $this->dispatch('defer-load-task', $taskId);
            }
        }
    }

    // This function will be called via JavaScript once the page is loaded
    public function openTaskFromQuery($taskId)
    {
        // Directly call the view modal open method
        $this->openViewModal($taskId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingAssigneeFilter()
    {
        $this->resetPage();
    }
    
    public function updatingRepetitiveFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->projectFilter = '';
        $this->priorityFilter = '';
        $this->statusFilter = '';
        $this->assigneeFilter = '';
        $this->repetitiveFilter = '';
        $this->resetPage();
    }

    public function deleteTask($taskId)
    {
        $params = [
            'component' => 'tasks.task-delete',
            'arguments' => [
                'taskId' => $taskId
            ]
        ];
        $this->dispatch('openModal', $params);
    }

    public function openCreateModal()
    {
        if (!auth()->user()->hasPermissionTo('create tasks')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to create tasks.'
            ]);
            return;
        }

        $dueDate = request()->query('due_date');
        
        $params = [
            'component' => 'tasks.task-create',
            'arguments' => [
                'due_date' => $dueDate
            ]
        ];
        $this->dispatch('openModal', $params);
    }

    public function openViewModal($taskId)
    {
        $params = [
            'component' => 'tasks.task-show',
            'arguments' => [
                'taskId' => $taskId
            ]
        ];
        $this->dispatch('openModal', $params);
    }

    public function openEditModal($taskId)
    {
        $params = [
            'component' => 'tasks.task-edit',
            'arguments' => [
                'taskId' => $taskId
            ]
        ];
        $this->dispatch('openModal', $params);
    }

    public function render()
    {
        $user = auth()->user();
        
        $tasks = Task::with(['project', 'createdBy', 'repetitiveTask'])
            ->when($this->search, function (Builder $query) {
                return $query->where(function (Builder $query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->projectFilter, function (Builder $query) {
                return $query->where('project_id', $this->projectFilter);
            })
            ->when($this->priorityFilter, function (Builder $query) {
                return $query->where('priority', $this->priorityFilter);
            })
            ->when($this->statusFilter, function (Builder $query) {
                return $query->where('current_status', $this->statusFilter);
            })
            ->when($this->assigneeFilter, function (Builder $query) {
                return $query->whereHas('taskAssignments', function (Builder $query) {
                    $query->where('user_id', $this->assigneeFilter);
                });
            })
            ->when($this->repetitiveFilter !== '', function (Builder $query) {
                if ($this->repetitiveFilter === 'yes') {
                    return $query->whereHas('repetitiveTask');
                } else {
                    return $query->whereDoesntHave('repetitiveTask');
                }
            });

        // Apply role-based visibility rules
        if ($user->hasRole('director')) {
            // Directors can see all tasks - no additional filtering needed
        } elseif ($user->hasRole('supervisor')) {
            // Supervisors can see all tasks in projects they supervise
            $tasks->whereIn('project_id', function($query) use ($user) {
                $query->select('id')
                    ->from('projects')
                    ->where('supervised_by', $user->id);
            });
        } else {
            // Regular users (team_leaders and employees) can only see tasks assigned to them
            $tasks->whereHas('taskAssignments', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $tasks = $tasks->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Get projects and users based on role
        if ($user->hasRole('director')) {
        $projects = Project::all();
        $users = User::all();
        } elseif ($user->hasRole('supervisor')) {
            $projects = Project::where('supervised_by', $user->id)->get();
            $users = User::whereIn('id', function($query) use ($projects) {
                $query->select('user_id')
                    ->from('project_members')
                    ->whereIn('project_id', $projects->pluck('id'));
            })->get();
        } else {
            $projectIds = $user->projectMembers()->pluck('project_id');
            $projects = Project::whereIn('id', $projectIds)->get();
            $users = User::whereIn('id', function($query) use ($projectIds) {
                $query->select('user_id')
                    ->from('project_members')
                    ->whereIn('project_id', $projectIds);
            })->get();
        }

        return view('livewire.tasks.task-list', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
} 