<div>
    <!-- Search and Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="md:col-span-2">
            <div class="relative">
                <input type="text" wire:model.live="search" placeholder="Search projects..." 
                       class="input input-bordered w-full pl-10">
                <span class="iconify lucide--search absolute left-3 top-1/2 -translate-y-1/2 text-base-content/50"></span>
            </div>
        </div>
        
        <div>
            <select wire:model.live="status" class="select select-bordered w-full">
                <option value="">All Status</option>
                <option value="planning">Planning</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="on_hold">On Hold</option>
            </select>
        </div>
    </div>
    
    <!-- Projects Grid View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($projects as $project)
            <div class="card bg-base-200 shadow-md hover:shadow-lg transition-shadow">
                <div class="card-body p-4">
                    <div class="flex justify-between items-start">
                        <div class="avatar">
                            <div class="bg-primary text-primary-content rounded-md w-10 flex items-center justify-center">
                                <span>{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                            </div>
                        </div>
                        <div class="badge {{ 
                            $project->status === 'completed' ? 'badge-success' : 
                            ($project->status === 'in_progress' ? 'badge-info' : 
                            ($project->status === 'on_hold' ? 'badge-warning' : 'badge-neutral')) 
                        }}">
                            {{ str_replace('_', ' ', ucfirst($project->status ?? 'Planning')) }}
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-lg mt-2">{{ $project->name }}</h3>
                    <p class="text-sm text-base-content/70 line-clamp-2">{{ $project->description }}</p>
                    
                    <div class="flex flex-col gap-2 mt-4">
                        <div class="flex justify-between text-sm">
                            <span>Start Date:</span>
                            <span>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>End Date:</span>
                            <span>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <div class="flex items-center gap-2">
                            <div class="avatar">
                                <div class="w-8 h-8 rounded-full bg-neutral text-neutral-content flex items-center justify-center">
                                    <span class="text-xs">{{ strtoupper(substr($project->createdBy->username ?? 'U', 0, 2)) }}</span>
                                </div>
                            </div>
                            @if($project->supervised_by)
                                <div class="avatar">
                                    <div class="w-8 h-8 rounded-full bg-primary text-primary-content flex items-center justify-center">
                                        <span class="text-xs">{{ strtoupper(substr($project->supervised_by->username ?? 'S', 0, 2)) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <a href="{{ route('projects.show', $project) }}" 
                           class="btn btn-sm btn-ghost">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10">
                <div class="text-base-content/70">No projects found</div>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="flex justify-center mt-6">
            {{ $projects->links() }}
        </div>
    @endif
</div>
