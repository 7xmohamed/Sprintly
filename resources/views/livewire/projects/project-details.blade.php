<div class="max-w-7xl mx-auto ">
    <!-- Project Header -->
    <div class="bg-base-100 shadow-xl rounded-box">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold">{{ $project->name }}</h1>
                    <p class="text-base-content/70 mt-2">{{ $project->description }}</p>
                </div>
                @if($canEdit || $canDelete)
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle">
                            <span class="iconify w-5 h-5" data-icon="solar:menu-dots-bold-duotone"></span>
                        </div>
                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                            @if($canEdit)
                                <li><a href="{{ route('projects.edit', $project) }}">
                                    <span class="iconify w-5 h-5 mr-2" data-icon="solar:pen-bold-duotone"></span> Edit Project
                                </a></li>
                            @endif
                            @if($canDelete)
                                <li>
                                    <button wire:click="confirmDeleteProject" class="text-error">
                                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:trash-bin-trash-bold-duotone"></span> Delete Project
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Project Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="stat bg-base-200 rounded-lg p-4 [border-inline-end:none!important]">
                    <div class="stat-title">Status</div>
                    <div class="stat-value">
                        <div class="dropdown">
                            @php
                                $statusClass = [
                                    'planning' => 'badge-info',
                                    'in_progress' => 'badge-primary',
                                    'on_hold' => 'badge-warning',
                                    'completed' => 'badge-success',
                                ][$project->status] ?? 'badge-secondary';
                                
                                $statusLabel = [
                                    'planning' => 'Planning',
                                    'in_progress' => 'In Progress',
                                    'on_hold' => 'On Hold',
                                    'completed' => 'Completed',
                                ][$project->status] ?? $project->status;
                            @endphp
                            
                            <div tabindex="0" class="badge {{ $statusClass }} badge-lg cursor-pointer m-1">
                                {{ $statusLabel }}
                                @if($canModifyStatus)
                                    <span class="iconify ml-1" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                                @endif
                            </div>
                            
                            @if($canModifyStatus)
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-lg bg-base-100 rounded-box w-52 mt-2">
                                    <li><button wire:click="updateStatus('planning')" class="{{ $project->status === 'planning' ? 'active' : '' }}">Planning</button></li>
                                    <li><button wire:click="updateStatus('in_progress')" class="{{ $project->status === 'in_progress' ? 'active' : '' }}">In Progress</button></li>
                                    <li><button wire:click="updateStatus('on_hold')" class="{{ $project->status === 'on_hold' ? 'active' : '' }}">On Hold</button></li>
                                    <li><button wire:click="updateStatus('completed')" class="{{ $project->status === 'completed' ? 'active' : '' }}">Completed</button></li>
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="stat bg-base-200 rounded-lg p-4 [border-inline-end:none!important]">
                    <div class="stat-title">Created By</div>
                    <div class="stat-value text-sm">{{ $project->createdBy->name }}</div>
                </div>

                <div class="stat bg-base-200 rounded-lg p-4 [border-inline-end:none!important]">
                    <div class="stat-title">Timeline</div>
                    <div class="stat-value text-sm">
                        {{ $project->start_date?->format('M d') ?? 'Not set' }} - {{ $project->end_date?->format('M d, Y') ?? 'Not set' }}
                    </div>
                </div>

                <div class="stat bg-base-200 rounded-lg p-4 [border-inline-end:none!important]">
                    <div class="stat-title">Supervisor</div>
                    <div class="stat-value text-sm">{{ $project->supervisedBy?->name ?? 'Not assigned' }}</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs tabs-bordered px-6">
            <button wire:click="setActiveTab('overview')" 
                class="tab tab-lg {{ $activeTab === 'overview' ? 'tab-active border-b-2 border-primary font-bold' : '' }}">
                Overview
            </button>
            <button wire:click="setActiveTab('tasks')" 
                class="tab tab-lg {{ $activeTab === 'tasks' ? 'tab-active border-b-2 border-primary font-bold' : '' }}">
                Tasks
            </button>
            <button wire:click="setActiveTab('members')" 
                class="tab tab-lg {{ $activeTab === 'members' ? 'tab-active border-b-2 border-primary font-bold ' : '' }}">
                Team Members
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="mt-6">
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project Information -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Project Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-base-content/70">Description</label>
                                <p class="mt-1">{{ $project->description }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-base-content/70">Department</label>
                                <p class="mt-1">{{ $project->department?->name ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-base-content/70">Budget</label>
                                <p class="mt-1">{{ $project->budget ? number_format($project->budget, 2) . ' MAD' : 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-base-content/70">Created At</label>
                                <p class="mt-1">{{ $project->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-base-content/70">Last Updated</label>
                                <p class="mt-1">{{ $project->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title">Team Members</h2>
                            <span class="badge badge-neutral">{{ $members->count() }} members</span>
                        </div>
                        <div class="space-y-4 mt-4">
                            @foreach($members->take(5) as $member)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar flex items-center justify-center">
                                            <div class="w-8 h-8">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}" class="w-full h-full object-cover" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $member->name }}</div>
                                            <div class="text-sm text-base-content/70">{{ $member->pivot->role }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-base-content/70">
                                        Joined {{ $member->pivot->joined_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                            @if($members->count() > 5)
                                <button wire:click="setActiveTab('members')" class="btn btn-ghost btn-sm w-full">
                                    View All Members
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'tasks')
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <h2 class="card-title">Tasks</h2>
                        @if($canCreateTasks)
                            <button class="btn btn-primary btn-sm" wire:click="openCreateModal">
                                <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span>
                                New Task
                            </button>
                        @endif
                    </div>
                    
                    <div class="overflow-x-auto mt-4">
                        @if($tasks->count() > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Created By</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>
                                                <span class="badge {{ 
                                                    $task->status === 'completed' ? 'badge-success' : 
                                                    ($task->status === 'in_progress' ? 'badge-primary' : 'badge-secondary') 
                                                }}">
                                                    {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</td>
                                            <td>{{ $task->createdBy->name }}</td>
                                            <td>
                                                <button class="btn btn-ghost btn-sm" wire:click="openViewModal({{ $task->id }})">
                                                    <span class="iconify w-5 h-5" data-icon="solar:eye-bold-duotone"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            @if($tasks->hasPages())
                                <div class="mt-4">
                                    {{ $tasks->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center mb-4">
                                    <span class="iconify w-8 h-8" data-icon="solar:check-circle-bold-duotone"></span>
                                </div>
                                <h3 class="text-lg font-semibold">No Tasks Yet</h3>
                                <p class="text-base-content/70 mt-1">Create your first task to get started</p>
                                @if($canCreateTasks)
                                    <button class="btn btn-primary mt-4" wire:click="openCreateModal">
                                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span>
                                        New Task
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'members')
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <h2 class="card-title">Team Members</h2>
                        @if($canManageMembers)
                            <button wire:click="$dispatch('openAddMemberModal')" class="btn btn-primary btn-sm">
                                <span class="iconify w-5 h-5 mr-2" data-icon="solar:user-plus-bold-duotone"></span>
                                Add Member
                            </button>
                        @endif
                    </div>
                    
                    <div class="overflow-x-auto mt-4 relative">
                        @if($members->count() > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        @if($canManageMembers)
                                            <th></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                        <tr>
                                            <td>
                                                <div class="flex items-center space-x-3">
                                                    <div class="avatar">
                                                        <div class="w-8 h-8">
                                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}" />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold">{{ $member->name }}</div>
                                                        <div class="text-sm text-base-content/70">{{ $member->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $member->pivot->role }}</td>
                                            <td>{{ $member->pivot->joined_at->format('M d, Y') }}</td>
                                            @if($canManageMembers)
                                                <td>
                                                    <button wire:click="confirmDelete({{ $member->id }})" class="btn btn-ghost btn-sm text-error">
                                                        <span class="iconify w-5 h-5" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center mb-4">
                                    <span class="iconify w-8 h-8" data-icon="solar:users-group-rounded-bold-duotone"></span>
                                </div>
                                <h3 class="text-lg font-semibold">No Team Members</h3>
                                <p class="text-base-content/70 mt-1">Add team members to get started</p>
                                @if($canManageMembers)
                                    <button wire:click="$dispatch('openAddMemberModal')" class="btn btn-primary mt-4">
                                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:user-plus-bold-duotone"></span>
                                        Add Member
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Add Member Modal -->
    <livewire:projects.add-member-modal :project="$project" />

    <!-- Delete Member Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-base-200 opacity-40" aria-hidden="true"></div>

                <div class="relative w-full max-w-lg p-6 my-8 overflow-hidden text-left transition-all transform bg-base-100 rounded-lg shadow-xl">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-error text-error-content rounded-full inline-flex items-center justify-center mb-4">
                            <span class="iconify w-8 h-8" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                        </div>
                        <h3 class="text-lg font-bold">Remove Member</h3>
                        <p class="py-4 text-base-content/70">Are you sure you want to remove this member from the project?</p>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="closeDeleteModal" class="btn">Cancel</button>
                        <button wire:click="deleteMember" class="btn btn-error">Remove Member</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Project Modal -->
    @if($showProjectDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-base-200 opacity-40" aria-hidden="true"></div>

                <div class="relative w-full max-w-lg p-6 my-8 overflow-hidden text-left transition-all transform bg-base-100 rounded-lg shadow-xl">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-error text-error-content rounded-full inline-flex items-center justify-center mb-4">
                            <span class="iconify w-8 h-8" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                        </div>
                        <h3 class="text-lg font-bold">Delete Project</h3>
                        <p class="py-4 text-base-content/70">Are you sure you want to delete this project? This action will remove all associated data and cannot be undone.</p>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="closeProjectDeleteModal" class="btn">Cancel</button>
                        <button wire:click="deleteProject" class="btn btn-error">Delete Project</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Modal Manager and Task Show Component -->
    <livewire:modals.modal-manager />
</div> 