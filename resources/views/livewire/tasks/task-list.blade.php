<div>
    <!-- Filters Section -->
    <div class="card bg-base-100 shadow-md mb-6">
        <div class="card-body">
            <div class="flex justify-between items-center mb-5">
                <h2 class="card-title">Tasks</h2>
                
                @if(auth()->user()->hasRole(['director', 'supervisor']))
                <button wire:click="openCreateModal()" class="btn btn-primary">
                    <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span> New Task
                </button>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Project</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="projectFilter">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Priority</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="priorityFilter">
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="todo">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Assignee</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="assigneeFilter">
                        <option value="">All Assignees</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Repetitive</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="repetitiveFilter">
                        <option value="">All Tasks</option>
                        <option value="yes">Repetitive Only</option>
                        <option value="no">Non-repetitive Only</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-between mt-4">
                <div class="form-control max-w-xs">
                    <div class="join">
                        <input class="input input-bordered join-item w-full" placeholder="Search tasks..." wire:model.live="search" />
                        <button class="btn join-item">
                            <span class="iconify w-5 h-5" data-icon="solar:magnifer-bold-duotone"></span>
                        </button>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button class="btn btn-outline btn-sm" wire:click="clearFilters">
                        <span class="iconify w-5 h-5 mr-1" data-icon="solar:close-circle-bold-duotone"></span> Clear Filters
                    </button>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-outline btn-sm">
                            <span class="iconify w-5 h-5 mr-1" data-icon="solar:sort-from-bottom-to-top-bold-duotone"></span> Sort By
                        </div>
                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a wire:click="sortBy('due_date')" class="cursor-pointer {{ $sortField === 'due_date' && $sortDirection === 'asc' ? 'active' : '' }}">Due Date (Earliest)</a></li>
                            <li><a wire:click="sortBy('due_date')" class="cursor-pointer {{ $sortField === 'due_date' && $sortDirection === 'desc' ? 'active' : '' }}">Due Date (Latest)</a></li>
                            <li><a wire:click="sortBy('priority')" class="cursor-pointer {{ $sortField === 'priority' && $sortDirection === 'desc' ? 'active' : '' }}">Priority (Highest)</a></li>
                            <li><a wire:click="sortBy('priority')" class="cursor-pointer {{ $sortField === 'priority' && $sortDirection === 'asc' ? 'active' : '' }}">Priority (Lowest)</a></li>
                            <li><a wire:click="sortBy('title')" class="cursor-pointer {{ $sortField === 'title' && $sortDirection === 'asc' ? 'active' : '' }}">Name (A-Z)</a></li>
                            <li><a wire:click="sortBy('title')" class="cursor-pointer {{ $sortField === 'title' && $sortDirection === 'desc' ? 'active' : '' }}">Name (Z-A)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="overflow-x-auto bg-base-100 rounded-lg shadow-md h-[500px] overflow-y-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th class="w-8">
                        <label>
                            <input type="checkbox" class="checkbox checkbox-sm" />
                        </label>
                    </th>
                    <th>Task</th>
                    <th>Project</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Assignee</th>
                    <th class="w-20">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr class="hover">
                        <td>
                            <label>
                                <input type="checkbox" class="checkbox checkbox-sm" />
                            </label>
                        </td>
                        <td>
                            <div>
                                <div class="font-bold hover:text-primary cursor-pointer" wire:click="openViewModal({{ $task->id }})">
                                    {{ $task->title }}
                                    @if($task->is_repetitive)
                                        <span class="badge badge-sm badge-accent ml-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M9.53 2.47a.75.75 0 0 0-1.06 1.06l.72.72H9a7.75 7.75 0 1 0 0 15.5h.5a.75.75 0 0 0 0-1.5H9a6.25 6.25 0 0 1 0-12.5h2a.75.75 0 0 0 .53-1.28z" clip-rule="evenodd"></path><path fill="currentColor" d="M14.5 4.25a.75.75 0 0 0 0 1.5h.5a6.25 6.25 0 1 1 0 12.5h-2a.75.75 0 0 0-.53 1.28l2 2a.75.75 0 0 0 1.06-1.06l-.72-.72H15a7.75 7.75 0 0 0 0-15.5z" opacity="0.5"></path></svg> Recurring
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-base-content/70 max-w-xs truncate">{{ $task->description }}</div>
                            </div>
                        </td>
                        <td>
                            <span>{{ $task->project->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @php
                                $isDirectorOrSupervisor = auth()->user()->hasRole(['director', 'supervisor']);
                                
                                if ($isDirectorOrSupervisor && $task->current_status === 'completed') {
                                    // For completed tasks, show approval status to directors and supervisors
                                    $statusClass = [
                                        'pending_approval' => 'badge-warning',
                                        'approved' => 'badge-success',
                                    ][$task->status] ?? 'badge-ghost';
                                    
                                    $statusLabel = [
                                        'pending_approval' => 'Pending Approval',
                                        'approved' => 'Approved',
                                    ][$task->status] ?? $task->status;
                                } else {
                                    // For non-completed tasks or non-director/supervisor users, show current status
                                    $statusClass = [
                                        'todo' => 'badge-secondary',
                                        'in_progress' => 'badge-primary',
                                        'completed' => 'badge-success',
                                    ][$task->current_status] ?? 'badge-ghost';
                                    
                                    $statusLabel = [
                                        'todo' => 'To Do',
                                        'in_progress' => 'In Progress',
                                        'completed' => 'Done',
                                    ][$task->current_status] ?? $task->current_status;
                                }
                            @endphp
                            <span class="badge badge-sm whitespace-nowrap !px-2 min-w-[60px] text-center {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            @php
                                $priorityClass = [
                                    'high' => 'badge-error',
                                    'medium' => 'badge-warning',
                                    'low' => 'badge-info',
                                ][$task->priority] ?? 'badge-ghost';
                            @endphp
                            <span class="badge badge-sm whitespace-nowrap !px-2 min-w-[60px] text-center {{ $priorityClass }}">{{ ucfirst($task->priority) }}</span>
                        </td>
                        <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                        <td>
                            @if($task->assignedUsers->count() > 0)
                                <div class="flex items-center gap-2">
                                    <div class="avatar-group -space-x-6">
                                        @foreach($task->assignedUsers->take(3) as $user)
                                            <div class="avatar">
                                                <div class="w-8 rounded-full">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}" />
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($task->assignedUsers->count() > 3)
                                            <div class="avatar placeholder">
                                                <div class="w-8 rounded-full bg-neutral-focus text-neutral-content">
                                                    <span>+{{ $task->assignedUsers->count() - 3 }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                                    <span class="iconify w-5 h-5" data-icon="solar:menu-dots-bold-duotone"></span>
                                </div>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a wire:click="openViewModal({{ $task->id }})" @click="document.activeElement.blur()">
                                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:eye-bold-duotone"></span> View Details
                                    </a></li>
                                    @if(auth()->user()->hasRole(['director', 'supervisor']))
                                    <li><a wire:click="openEditModal({{ $task->id }})" @click="document.activeElement.blur()">
                                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:pen-bold-duotone"></span> Edit Task
                                    </a></li>
                                    <li><a wire:click="deleteTask({{ $task->id }})" @click="document.activeElement.blur()" class="text-error">
                                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:trash-bin-trash-bold-duotone"></span> Delete Task
                                    </a></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center">
                                    <span class="iconify w-8 h-8" data-icon="solar:clipboard-list-bold-duotone"></span>
                                </div>
                                <p class="text-gray-500">No tasks found</p>
                                @if(auth()->user()->hasPermissionTo('create tasks'))
                                <button wire:click="openCreateModal()" class="btn btn-sm btn-primary">
                                    <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span> Create a task
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($tasks->hasPages())
        <div class="pt-4">
            {{ $tasks->links() }}
        </div>
    @endif
    
    <script>
        // Direct DOM initialization - runs immediately
        document.addEventListener('DOMContentLoaded', function() {
            // Check for URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const taskIdFromUrl = urlParams.get('open_task');
            const editTaskIdFromUrl = urlParams.get('edit_task');
            const newTask = urlParams.get('new_task');
            
            if (newTask === 'true') {
                console.log('New task parameter found in URL');
                if (window.Livewire) {
                    console.log('Livewire available, opening create modal...');
                    setTimeout(() => window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).openCreateModal(), 500);
                }
            } else if (editTaskIdFromUrl) {
                console.log('Edit Task ID found in URL:', editTaskIdFromUrl);
                if (window.Livewire) {
                    console.log('Livewire already available, opening edit modal...');
                    setTimeout(() => window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).openEditModal(editTaskIdFromUrl), 500);
                }
            } else if (taskIdFromUrl) {
                console.log('View Task ID found in URL:', taskIdFromUrl);
                if (window.Livewire) {
                    console.log('Livewire already available, opening view modal...');
                    setTimeout(() => window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).openViewModal(taskIdFromUrl), 500);
                }
            }
        });
        
        // Livewire initialization - may run after DOM is ready
        document.addEventListener('livewire:initialized', () => {
            console.log('Livewire initialized');
            
            // Check for URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const taskIdFromUrl = urlParams.get('open_task');
            const editTaskIdFromUrl = urlParams.get('edit_task');
            const newTask = urlParams.get('new_task');
            
            if (newTask === 'true') {
                console.log('New task parameter found in URL after Livewire init');
                setTimeout(() => {
                    console.log('Opening create modal');
                    @this.openCreateModal();
                }, 500);
            } else if (editTaskIdFromUrl) {
                console.log('Edit Task ID found in URL after Livewire init:', editTaskIdFromUrl);
                setTimeout(() => {
                    console.log('Opening edit modal for ID:', editTaskIdFromUrl);
                    @this.openEditModal(editTaskIdFromUrl);
                }, 500);
            } else if (taskIdFromUrl) {
                console.log('View Task ID found in URL after Livewire init:', taskIdFromUrl);
                setTimeout(() => {
                    console.log('Opening view modal for ID:', taskIdFromUrl);
                    @this.openViewModal(taskIdFromUrl);
                }, 500);
            }
            
            // Listen for view modal event
            Livewire.on('defer-load-task', (taskId) => {
                console.log('Received defer-load-task event with ID:', taskId);
                setTimeout(() => {
                    @this.openViewModal(taskId);
                }, 500);
            });
            
            // Listen for edit modal event
            Livewire.on('defer-load-task-edit', (taskId) => {
                console.log('Received defer-load-task-edit event with ID:', taskId);
                setTimeout(() => {
                    @this.openEditModal(taskId);
                }, 500);
            });

            // Listen for create modal event
            Livewire.on('defer-load-task-create', (data) => {
                console.log('Received defer-load-task-create event');
                setTimeout(() => {
                    @this.openCreateModal();
                }, 500);
            });
        });
    </script>
</div> 