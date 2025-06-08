<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\ProjectMember;
// use App\Models\Chat;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProjectList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $projectToDelete = null;
    public $showDeleteModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
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

    public function confirmDelete($projectId)
    {
        if (!auth()->user()->hasPermissionTo('delete projects')) {
            abort(403, 'Unauthorized action.');
        }

        $this->projectToDelete = $projectId;
        $this->showDeleteModal = true;
    }

    public function deleteProject()
    {
        if (!auth()->user()->hasPermissionTo('delete projects')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $project = Project::findOrFail($this->projectToDelete);
            // dd($project);
            // Delete project members
            ProjectMember::where('project_id', $project->id)->delete();

            // Delete associated chat data
            // Chat::where('project_id', $project->id)->delete();

            // Delete the project
            $project->delete();

            $this->showDeleteModal = false;
            $this->projectToDelete = null;

            session()->flash('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete project.');
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->projectToDelete = null;
    }

    public function render()
    {
        $query = Project::query()
            ->with(['createdBy', 'supervisedBy', 'members']);

        // Apply permission-based filtering
        if (auth()->user()->hasPermissionTo('view all projects')) {
            // Director can see all projects
            $query->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            });
        } else if (auth()->user()->hasPermissionTo('view assigned projects')) {
            // For supervisors and employees, show only their assigned projects
            $query->where(function($query) {
                $query->whereHas('members', function ($query) {
                    $query->where('user_id', auth()->id());
                })->orWhere('supervised_by', auth()->id());
            })->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            });
        } else {
            // No permission to view projects
            $query->where('id', 0); // Return empty result
        }

        // Apply status filter if selected
        $query->when($this->statusFilter, function ($query) {
            $query->where('status', $this->statusFilter);
        });

        $projects = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.projects.project-list', [
            'projects' => $projects,
            'canCreate' => auth()->user()->hasPermissionTo('create projects'),
            'canEdit' => auth()->user()->hasPermissionTo('edit projects'),
            'canDelete' => auth()->user()->hasPermissionTo('delete projects')
        ]);
    }
} 