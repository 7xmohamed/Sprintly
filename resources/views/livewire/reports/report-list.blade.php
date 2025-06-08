<div>
    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header with Create Button -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Daily Reports</h2>
        @hasrole('team_leader')
        <a href="{{ route('reports.create') }}" class="btn btn-primary">
            <span class="iconify w-5 h-5 mr-1" data-icon="solar:add-square-bold-duotone"></span>
            Create Report
        </a>
        @endhasrole
    </div>

    <!-- Report Controls -->
    <div class="card bg-base-100 shadow-md mb-6">
        <div class="card-body p-4">
            <div class="flex flex-col lg:flex-row justify-between gap-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="form-control w-full sm:max-w-xs">
                        <label class="label">
                            <span class="label-text">Date Range</span>
                        </label>
                        <select class="select select-bordered w-full" wire:model.live="dateRange" wire:change="setDateRange($event.target.value)">
                            @foreach($dateRangeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-control w-full sm:max-w-xs">
                        <label class="label">
                            <span class="label-text">Project</span>
                        </label>
                        <select class="select select-bordered w-full" wire:model.live="projectFilter">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-control w-full sm:max-w-xs">
                        <label class="label">
                            <span class="label-text">Department</span>
                        </label>
                        <select class="select select-bordered w-full" wire:model.live="departmentFilter">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-control w-full lg:max-w-xs">
                    <label class="label">
                        <span class="label-text">Search Assignee</span>
                    </label>
                    <div class="join w-full">
                        <input class="input input-bordered join-item w-full" 
                               wire:model.live.debounce.300ms="searchAssignee" 
                               placeholder="Search by name..." />
                        <button class="btn join-item">
                            <span class="iconify w-5 h-5" data-icon="solar:magnifer-bold-duotone"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports List -->
    <div class="space-y-6">
        @forelse($reports as $report)
            <div class="card bg-base-100 shadow-md">
                <div class="card-body p-4">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-4">
                            <div class="avatar">
                                <div class="w-12 rounded-full">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($report->project?->name ?? 'Project') }}" 
                                         alt="{{ $report->project?->name ?? 'Project' }}" />
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $report->project?->name ?? 'No Project' }}</h3>
                                <p class="text-sm text-base-content/70">{{ $report->date->format('F j, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="btn btn-sm btn-ghost" wire:click="showAssigneeReport({{ $report->user_id }})">
                                <span class="iconify w-4 h-4" data-icon="solar:eye-bold-duotone"></span>
                            </button>
                            @if(auth()->id() === $report->user_id || auth()->user()->hasRole(['director', 'supervisor']))
                                <button wire:click="showEditReport({{ $report->id }})" class="btn btn-sm btn-ghost">
                                    <span class="iconify w-4 h-4" data-icon="solar:pen-2-bold-duotone"></span>
                                </button>
                                <button wire:click="showDeleteReport({{ $report->id }})" class="btn btn-sm btn-ghost text-error">
                                    <span class="iconify w-4 h-4" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-base-content/80">{{ Str::limit($report->summary, 150) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center mb-4 mx-auto">
                    <span class="iconify w-8 h-8" data-icon="solar:notebook-bold-duotone"></span>
                </div>
                <h3 class="text-lg font-semibold">No Reports Found</h3>
                <p class="text-base-content/70 mt-1">Try adjusting your filters</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-8">
        {{ $reports->links() }}
    </div>

    <!-- Assignee Report Modal -->
    @if($showAssigneeModal && $selectedAssigneeId)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-5xl">
                <livewire:reports.assignee-report-modal :userId="$selectedAssigneeId" :key="$selectedAssigneeId" />
            </div>
            <div class="modal-backdrop" wire:click="closeAssigneeModal">
                <button class="cursor-pointer">close</button>
            </div>
        </div>
    @endif

    <!-- Edit Report Modal -->
    @if($showEditModal && $selectedReportId)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-5xl">
                <livewire:reports.edit-report-modal :reportId="$selectedReportId" :key="$selectedReportId" />
            </div>
            <div class="modal-backdrop" wire:click="closeEditModal">
                <button class="cursor-pointer">close</button>
            </div>
        </div>
    @endif

    <!-- Delete Report Modal -->
    @if($showDeleteModal && $reportToDeleteId)
        <div class="modal modal-open">
            <div class="modal-box">
                <livewire:reports.delete-report-modal :reportId="$reportToDeleteId" />
            </div>
            <div class="modal-backdrop" wire:click="closeDeleteModal">
                <button class="cursor-pointer">close</button>
            </div>
        </div>
    @endif
</div>
