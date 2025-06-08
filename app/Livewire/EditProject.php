<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\User;
use App\Models\Department;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class EditProject extends Component
{
    public Project $project;
    
    public $name;
    public $description;
    public $department_id;
    public $start_date;
    public $end_date;
    public $status;
    public $supervised_by;
    public $team_leader_id;
    public $budget;
    public $selectedTeamMembers = [];
    public $teamMembers = [];
    public $send_notifications = true;
    public $departmentMembers = [];
    public $availableSupervisors = [];

    // Add message properties
    public $showMessage = false;
    public $message = '';
    public $messageType = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'status' => 'required|in:planning,in_progress,completed,on_hold',
        'supervised_by' => 'required|exists:users,id',
        'team_leader_id' => 'required|exists:users,id',
        'budget' => 'nullable|numeric|min:0',
        'selectedTeamMembers' => 'required|array|min:1',
        'selectedTeamMembers.*' => 'exists:users,id'
    ];

    public function mount(Project $project)
    {
        if (!auth()->user()->hasPermissionTo('edit projects')) {
            abort(403, 'You do not have permission to edit projects.');
        }

        $this->project = $project;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->department_id = $project->department_id;
        $this->start_date = $project->start_date?->format('Y-m-d');
        $this->end_date = $project->end_date?->format('Y-m-d');
        $this->status = $project->status;
        $this->budget = $project->budget;
        
        // Load supervisor
        $this->supervised_by = $project->supervised_by;
        
        // First load selected team members
        $this->selectedTeamMembers = $project->members()
            ->pluck('users.id')
            ->toArray();
        
        // Then load team members for dropdown
        $this->teamMembers = User::whereIn('id', $this->selectedTeamMembers)
            ->select('users.*')
            ->get();

        // Load team leader
        $teamLeader = $project->members()
            ->where('project_members.role', 'team_leader')
            ->first();
        $this->team_leader_id = $teamLeader?->id;

        // Load department members and supervisors for selection
        if ($this->department_id) {
            $this->loadDepartmentMembers();
            $this->loadSupervisors();
        }
    }
 
    public function updatedSelectedTeamMembers()
    {
        $this->teamMembers = User::whereIn('id', $this->selectedTeamMembers)
            ->select('id', 'first_name', 'last_name')
            ->get();
    }

    public function updatedDepartmentId($value)
    {
        $this->selectedTeamMembers = [];
        // Reset all member-related fields
        $this->reset([
            'team_leader_id',
            'supervised_by'
        ]);

        // Clear existing team members
        $this->teamMembers = collect();
        
        // Load new department members if a department is selected
        if ($value) {
            $this->loadDepartmentMembers();
            $this->loadSupervisors();
        } else {
            $this->departmentMembers = collect();
            $this->availableSupervisors = collect();
        }
    }

    /**
     * Load department members including those with secondary assignments
     */
    private function loadDepartmentMembers()
    {
        $this->departmentMembers = User::whereHas('departments', function($query) {
                $query->where('departments.id', $this->department_id);
            })
            ->where('role', 'employee')
            ->get();
    }

    /**
     * Load all supervisors, prioritizing those from the current department
     */
    private function loadSupervisors()
    {
        // Get all supervisors
        $this->availableSupervisors = User::where('role', 'supervisor')
            ->orderByRaw("CASE 
                WHEN department_id = ? THEN 0 
                WHEN id IN (
                    SELECT user_id 
                    FROM user_departments 
                    WHERE department_id = ?
                ) THEN 1 
                ELSE 2 
            END", [$this->department_id, $this->department_id])
            ->get();
    }

    private function showMessage($message, $type)
    {
        $this->message = $message;
        $this->messageType = $type;
        $this->showMessage = true;
    }

    public function update()
    {
        if (!auth()->user()->hasPermissionTo('edit projects')) {
            $this->showMessage('You do not have permission to edit projects.', 'error');
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Get the current team leader before update
            $currentTeamLeader = $this->project->members()
                ->where('project_members.role', 'team_leader')
                ->first();

            // Store original members and their roles before update - Fixed to use project_members table
            $originalMembers = $this->project->members()
                ->withPivot('role')
                ->get()
                ->mapWithKeys(function ($member) {
                    return [$member->id => $member->pivot->role];
                })
                ->toArray();
            
            // Store original supervisor
            $originalSupervisor = $this->project->supervised_by;

            // Update project basic information
            $this->project->update([
                'name' => $this->name,
                'description' => $this->description,
                'department_id' => $this->department_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'status' => $this->status,
                'budget' => $this->budget,
                'supervised_by' => $this->supervised_by,
            ]);

            // Handle role changes if team leader has changed
            if ($currentTeamLeader && $currentTeamLeader->id != $this->team_leader_id) {
                $currentTeamLeader->removeRole('team_leader');
                $currentTeamLeader->assignRole('employee');
            }

            // Assign team_leader role to the new team leader
            $newTeamLeader = User::find($this->team_leader_id);
            if ($newTeamLeader) {
                $newTeamLeader->syncRoles(['team_leader']);
            }

            // Prepare member data with roles
            $memberData = [];

            // Add team manager as team_leader
            if ($this->team_leader_id) {
                $memberData[$this->team_leader_id] = [
                    'role' => 'team_leader',
                    'joined_at' => now()
                ];
            }

            // Add regular team members
            foreach ($this->selectedTeamMembers as $memberId) {
                if ($memberId != $this->team_leader_id) {
                    $memberData[$memberId] = [
                        'role' => 'member',
                        'joined_at' => now()
                    ];
                }
            }

            // Get removed members (in original but not in new memberData)
            $removedMembers = array_diff(array_keys($originalMembers), array_keys($memberData));

            // Sync all members with their roles
            $this->project->members()->sync($memberData);

            if ($this->send_notifications) {
                // Notify new supervisor if changed
                if ($originalSupervisor !== $this->supervised_by) {
                    Notification::create([
                        'user_id' => $this->supervised_by,
                        'from_id' => auth()->id(),
                        'title' => 'Project Supervision Assignment',
                        'message' => "You have been assigned as supervisor for project: {$this->project->name}",
                        'type' => 'assignment',
                        'data' => json_encode([
                            'project_id' => $this->project->id,
                            'project_name' => $this->project->name,
                            'role' => 'supervisor',
                            'updated_by' => auth()->user()->name
                        ]),
                        'is_read' => false
                    ]);
                }

                // Send notifications only to new members or members with role changes
                foreach ($memberData as $memberId => $data) {
                    $shouldNotify = false;
                    $roleTitle = ucfirst(str_replace('_', ' ', $data['role']));
                    
                    // Check if member is new or role has changed
                    if (!isset($originalMembers[$memberId])) {
                        // New member
                        $shouldNotify = true;
                    } elseif ($originalMembers[$memberId] !== $data['role']) {
                        // Existing member but role changed
                        $shouldNotify = true;
                    }

                    if ($shouldNotify && $memberId !== auth()->id()) { // Don't notify the user making the changes
                        Notification::create([
                            'user_id' => $memberId,
                            'from_id' => auth()->id(),
                            'title' => 'Project Role Assignment',
                            'message' => "You have been assigned as {$roleTitle} for project: {$this->project->name}",
                            'type' => 'assignment',
                            'data' => json_encode([
                                'project_id' => $this->project->id,
                                'project_name' => $this->project->name,
                                'role' => $data['role'],
                                'updated_by' => auth()->user()->name
                            ]),
                            'is_read' => false
                        ]);
                    }
                }

                // Notify removed members
                foreach ($removedMembers as $memberId) {
                    if ($memberId !== auth()->id()) { // Don't notify the user making the changes
                Notification::create([
                            'user_id' => $memberId,
                            'from_id' => auth()->id(),
                            'title' => 'Removed from Project',
                            'message' => "You have been removed from project: {$this->project->name}",
                            'type' => 'assignment',
                            'data' => json_encode([
                            'project_id' => $this->project->id,
                            'project_name' => $this->project->name,
                            'updated_by' => auth()->user()->name
                            ]),
                    'is_read' => false
                ]);
                    }
                }
            }

            DB::commit();

            session()->flash('notify', [
                'type' => 'success',
                'message' => 'Project updated successfully!'
            ]);

            return redirect()->route('projects.show', $this->project);

        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('notify', [
                'type' => 'error',
                'message' => 'Failed to update project: ' . $e->getMessage()
            ]);
            
            return null;
        }
    }

    public function render()
    {
        return view('livewire.edit-project', [
            'departments' => Department::all(),
            'departmentMembers' => $this->departmentMembers,
            'supervisors' => $this->availableSupervisors,
        ]);
    }
} 