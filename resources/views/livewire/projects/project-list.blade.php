<div>
    <!-- Filters Section -->
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <div class="flex justify-between items-center mb-5">
                <h2 class="card-title">Projects</h2>
                
                @if($canCreate)
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span> New Project
                    </a>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <div class="join w-full">
                        <input type="text" wire:model.live="search" class="input input-bordered join-item w-full" placeholder="Search projects...">
                        <button class="btn join-item">
                            <span class="iconify w-5 h-5" data-icon="solar:magnifer-bold-duotone"></span>
                        </button>
                    </div>
                </div>
                
                <div class="form-control">
                    <select wire:model.live="statusFilter" class="select select-bordered w-full">
                        <option value="">All Status</option>
                        <option value="planning">Planning</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="on_hold">On Hold</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($projects as $project)
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-start">
                        <div class="w-12 h-12 bg-neutral text-neutral-content rounded-lg inline-flex items-center justify-center">
                            <span class="text-xl font-medium">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                        </div>
                        @if($canEdit || $canDelete)
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle">
                                    <span class="iconify w-5 h-5" data-icon="solar:menu-dots-bold-duotone"></span>
                                </div>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    @if($canEdit)
                                        <li><a href="{{ route('projects.edit', $project) }}">
                                            <span class="iconify w-5 h-5 mr-2" data-icon="solar:pen-bold-duotone"></span> Edit
                                        </a></li>
                                    @endif
                                    @if($canDelete)
                                        <li>
                                            <button wire:click="confirmDelete('{{ $project->id }}')" class="text-error">
                                                <span class="iconify w-5 h-5 mr-2" data-icon="solar:trash-bin-trash-bold-duotone"></span> Delete
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>

                    <h2 class="card-title mt-4">{{ $project->name }}</h2>
                    <p class="text-base-content/70 line-clamp-2">{{ $project->description }}</p>

                    <div class="flex flex-col gap-2 mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-base-content/70">Status</span>
                            <span class="badge {{ 
                                $project->status === 'completed' ? 'badge-success' : 
                                ($project->status === 'in_progress' ? 'badge-primary' : 
                                ($project->status === 'on_hold' ? 'badge-warning' : 'badge-secondary')) 
                            }}">
                                {{ str_replace('_', ' ', ucfirst($project->status)) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-base-content/70">Created By</span>
                            <span>{{ $project->createdBy->name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-base-content/70">Timeline</span>
                            <span>{{ $project->start_date?->format('M d') ?? 'Not set' }} - {{ $project->end_date?->format('M d, Y') ?? 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="card-actions justify-between items-center mt-4">
                        <div class="flex -space-x-3">
                            @forelse($project->members as $member)
                                @if($loop->iteration <= 3)
                                <div class="w-8 h-8 rounded-full border-2 border-base-100 overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}" 
                                         class="w-full h-full object-cover"
                                         alt="{{ $member->name }}" />
                                </div>
                                @endif
                            @empty
                                <div class="w-8 h-8 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center">
                                    <span class="text-sm font-medium">0</span>
                                </div>
                            @endforelse
                            @if($project->members->count() > 3)
                                <div class="w-8 h-8 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center border-2 border-base-100">
                                    <span class="text-sm font-medium">+{{ $project->members->count() - 3 }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center mb-4 mx-auto">
                        <span class="iconify w-8 h-8" data-icon="solar:folder-bold-duotone"></span>
                    </div>
                    <h3 class="text-lg font-semibold">No Projects Found</h3>
                    <p class="text-base-content/70 mt-1">Get started by creating a new project</p>
                    @if($canCreate)
                        <a href="{{ route('projects.create') }}" class="btn btn-primary mt-4">
                            <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span> New Project
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
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
                        <button wire:click="closeDeleteModal" class="btn">Cancel</button>
                        <button wire:click="deleteProject" class="btn btn-error">Delete Project</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> 