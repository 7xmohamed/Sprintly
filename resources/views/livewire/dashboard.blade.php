<div class="space-y-6">
    {{-- Care about people's approval and you will be their prisoner. --}}

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Projects Card -->
        @if(auth()->user()->hasPermissionTo('view all projects') || auth()->user()->hasPermissionTo('view assigned projects'))
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">Active Projects</p>
                        <div class="mt-1">
                            <p class="text-2xl font-semibold">{{ $activeProjects }}</p>
                        </div>
                    </div>
                    <div class="bg-primary/10 p-3 rounded-full">
                        <span class="iconify w-6 h-6 text-primary" data-icon="solar:chart-2-bold-duotone"></span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tasks Completed Card -->
        @if(auth()->user()->hasPermissionTo('view all tasks') || auth()->user()->hasPermissionTo('view assigned tasks'))
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">
                            @if(auth()->user()->hasRole(['director', 'supervisor']))
                                Tasks Completed
                            @else
                                My Completed Tasks
                            @endif
                        </p>
                        
                        <div class="mt-1">
                            <p class="text-2xl font-semibold">{{ $completedTasks }}</p>
                        </div>
                    </div>
                    <div class="bg-success/10 p-3 rounded-full">
                        <span class="iconify w-6 h-6 text-success" data-icon="solar:check-square-bold-duotone"></span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Pending Tasks Card -->
        @if(auth()->user()->hasPermissionTo('view all tasks') || auth()->user()->hasPermissionTo('view assigned tasks'))
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">
                            @if(auth()->user()->hasRole(['director', 'supervisor']))
                                Pending Tasks
                            @else
                                My Pending Tasks
                            @endif
                        </p>
                        <div class="mt-1">
                            <p class="text-2xl font-semibold">{{ $pendingTasks }}</p>
                        </div>
                    </div>
                    <div class="bg-warning/10 p-3 rounded-full">
                        <span class="iconify w-6 h-6 text-warning" data-icon="solar:clock-circle-bold-duotone"></span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Team Members / Projects Card -->
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">
                            @if(auth()->user()->hasRole('director'))
                                Team Members
                            @elseif(auth()->user()->hasRole('supervisor'))
                                Total Supervised Projects
                            @else
                                Total Joined Projects
                            @endif
                        </p>
                        <div class="mt-1">
                            <p class="text-2xl font-semibold">{{ $teamMembers }}</p>
                        </div>
                    </div>
                    <div class="bg-secondary/10 p-3 rounded-full">
                        @if(auth()->user()->hasRole('director'))
                            <span class="iconify w-6 h-6 text-secondary" data-icon="solar:users-group-rounded-bold-duotone"></span>
                        @else
                            <span class="iconify w-6 h-6 text-secondary" data-icon="solar:folder-with-files-bold-duotone"></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Overview -->
    @if(auth()->user()->hasPermissionTo('view all projects') || auth()->user()->hasPermissionTo('view assigned projects'))
    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="card-title">Projects Overview</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Team</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProjects as $project)
                            <tr>
                                <td class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-base-200 flex items-center justify-center">
                                        <span class="iconify w-6 h-6" data-icon="solar:laptop-bold-duotone"></span>
                                    </div>
                                    <span>{{ $project->name }}</span>
                                </td>
                                <td>
                                    <div class="badge badge-{{ $project->status === 'completed' ? 'success' : ($project->status === 'active' ? 'primary' : 'ghost') }} badge-sm">
                                        {{ $project->status }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex -space-x-4">
                                        @foreach($project->members->take(3) as $member)
                                            <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-base-100">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=random" alt="{{ $member->name }}" class="w-full h-full object-cover" />
                                            </div>
                                        @endforeach
                                        @if($project->members->count() > 3)
                                            <div class="w-8 h-8 rounded-full bg-base-300 border-2 border-base-100 flex items-center justify-center text-xs font-medium">
                                                +{{ $project->members->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-base-content/70">{{ $project->end_date ? $project->end_date->format('d M Y') : 'No deadline' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No projects found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Pending Approvals and Tasks -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Approvals -->
        @if($canApproveTask)
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title flex justify-between">
                    <span>Pending Approvals</span>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-sm btn-ghost">
                            <span class="iconify w-4 h-4" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                        </div>
                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a href="{{ route('tasks.index') }}">View All Tasks</a></li>
                        </ul>
                    </div>
                </h2>
                
                <div class="space-y-4">
                    @forelse($pendingApprovals as $task)
                        <div class="card bg-base-200">
                            <div class="card-body p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium">{{ $task->title }}</h3>
                                        <p class="text-sm text-base-content/70">{{ $task->project->name ?? 'No Project' }}</p>
                                        <p class="text-sm mt-2">{{ Str::limit($task->description, 100) }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="w-6 h-6 bg-accent text-accent-content rounded-full inline-flex items-center justify-center">
                                                <span class="text-xs font-medium">{{ $task->createdBy ? substr($task->createdBy->name, 0, 2) : 'NA' }}</span>
                                            </div>
                                            <span class="text-xs">Requested by {{ $task->createdBy?->name ?? 'Unknown' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button wire:click="approveTask({{ $task->id }})" class="btn btn-sm btn-success">Approve</button>
                                        <button wire:click="rejectTask({{ $task->id }})" class="btn btn-sm btn-error">Reject</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-base-content/70">
                            No pending approvals
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        <!-- Tasks Due Soon -->
        @if(auth()->user()->hasPermissionTo('view all tasks') || auth()->user()->hasPermissionTo('view assigned tasks'))
        <div class="card bg-base-100 shadow-md {{ !$canApproveTask ? 'lg:col-span-2' : '' }}">
            <div class="card-body">
                <h2 class="card-title flex justify-between">
                    <span>Tasks Due Soon</span>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-sm btn-ghost">
                            <span class="iconify w-4 h-4" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                        </div>
                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a href="{{ route('tasks.index') }}">View All Tasks</a></li>
                            @if(auth()->user()->hasPermissionTo('create tasks'))
                            <li><a href="{{ route('tasks.create') }}">Add New Task</a></li>
                            @endif
                        </ul>
                    </div>
                </h2>
                
                <div class="space-y-4">
                    @forelse($tasksDueSoon as $task)
                        <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                            <div>
                                <h3 class="font-medium">{{ $task->title }}</h3>
                                <p class="text-sm text-base-content/70">{{ $task->project->name ?? 'No Project' }}</p>
                            </div>
                            <div class="text-right">
                                <div class="badge badge-{{ Carbon\Carbon::parse($task->due_date)->isToday() ? 'error' : 'warning' }}">
                                    {{ Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                                </div>
                                <div class="mt-1 flex justify-end -space-x-2">
                                    @foreach($task->assignedUsers->take(2) as $user)
                                        <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-base-100">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                                        </div>
                                    @endforeach
                                    @if($task->assignedUsers->count() > 2)
                                        <div class="w-8 h-8 rounded-full bg-base-300 border-2 border-base-100 flex items-center justify-center text-xs font-medium">
                                            +{{ $task->assignedUsers->count() - 2 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-base-content/70">
                            No tasks due soon
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
