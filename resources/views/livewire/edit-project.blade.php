<div>
    <form wire:submit.prevent="update" id="edit-project-form" class="space-y-6 max-w-5xl mx-auto">
        <!-- Message Display -->
        @if($showMessage)
            <div class="fixed top-4 right-4 z-50 animate-fade-in-down">
                <div class="rounded-md p-4 {{ $messageType === 'success' ? 'bg-green-50' : 'bg-red-50' }} shadow-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if($messageType === 'success')
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="{{ $messageType === 'success' ? 'text-green-800' : 'text-red-800' }}">
                                {{ $message }}
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button wire:click="$set('showMessage', false)" class="{{ $messageType === 'success' ? 'text-green-500 hover:text-green-600' : 'text-red-500 hover:text-red-600' }} rounded-md p-1.5">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Basic Information Section -->
        <div class="card bg-base-100 shadow-xl form-section">
            <div class="card-body">
                <h2 class="card-title text-xl flex items-center gap-2">
                    <span class="iconify w-5 h-5" data-icon="solar:info-circle-bold-duotone"></span> Basic Information
                </h2>
                <p class="text-sm text-base-content/70 mb-4">Update the essential details about your project</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Project Name</span>
                        </label>
                        <input type="text" wire:model="name" placeholder="Enter project name" class="input input-bordered w-full" required />
                        <label class="label">
                            <span class="label-text-alt">Choose a clear, descriptive name</span>
                        </label>
                        @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Department</span>
                        </label>
                        <select wire:model.live="department_id" class="select select-bordered w-full" required>
                            <option value="">Select department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ $department->id == $project->department_id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Start Date</span>
                        </label>
                        <input type="date" wire:model="start_date" class="input input-bordered w-full" required />
                        @error('start_date') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">End Date</span>
                        </label>
                        <input type="date" wire:model="end_date" class="input input-bordered w-full" required />
                        @error('end_date') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Status</span>
                        </label>
                        <select wire:model="status" class="select select-bordered w-full" required>
                            <option value="">Select status</option>
                            <option value="planning">Planning</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                        @error('status') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="form-control w-full mt-4">
                    <label class="label">
                        <span class="label-text required-field">Project Description</span>
                    </label>
                    <textarea wire:model="description" class="textarea textarea-bordered h-32 w-full" placeholder="Provide a detailed description of the project objectives, scope, and expected outcomes" required></textarea>
                    @error('description') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Team & Resources Section -->
        <div class="card bg-base-100 shadow-xl form-section">
            <div class="card-body">
                <h2 class="card-title text-xl flex items-center gap-2">
                    <span class="iconify w-5 h-5" data-icon="solar:users-group-two-rounded-bold-duotone"></span> Team & Resources
                </h2>
                <p class="text-sm text-base-content/70 mb-4">Assign project members and allocate resources</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Project Supervisor</span>
                        </label>
                        <select wire:model="supervised_by" class="select select-bordered w-full" required>
                            <option value="">Select project supervisor</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->first_name }} {{ $supervisor->last_name }}</option>
                            @endforeach
                        </select>
                        @error('supervised_by') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Team Manager</span>
                        </label>
                        <select wire:model="team_leader_id" class="select select-bordered w-full" required>
                            <option value="">Select team manager</option>
                            @foreach($teamMembers as $member)
                                <option value="{{ $member->id }}" {{ $member->id == $team_leader_id ? 'selected' : '' }}>
                                    {{ $member->first_name }} {{ $member->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('team_leader_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-control w-full">
                    <div class="form-control mt-4 w-full">
                        <label class="label">
                            <span class="label-text">Budget</span>
                        </label>
                        <div class="join w-full">
                            <input type="number" wire:model="budget" placeholder="0.00" min="0" step="0.01" class="input input-bordered join-item w-full" />
                            <span class="join-item flex items-center px-3 bg-base-200 border border-r-0 border-base-300">DH</span>
                        </div>
                        @error('budget') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-control w-full mt-4">
                    <label class="label">
                        <span class="label-text">Team Members</span>
                    </label>
                    <div class="bg-base-200 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @if($departmentMembers)
                                @foreach($departmentMembers as $member)
                                    <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-base-300 rounded-md" wire:key="department-{{ $department_id }}-member-{{ $member->id }}">
                                        <input type="checkbox" 
                                            wire:model.live="selectedTeamMembers" 
                                            value="{{ $member->id }}" 
                                            class="checkbox checkbox-sm"
                                            @if($member->id === $supervised_by) disabled @endif
                                        />
                                        <div class="flex items-center gap-2">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content w-8 rounded-full">
                                                    <span>{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                                                @if($member->id === $supervised_by)
                                                    <span class="badge badge-sm">Supervisor</span>
                                                @elseif($member->id === $team_leader_id)
                                                    <span class="badge badge-sm">Team Manager</span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @error('selectedTeamMembers') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Additional Settings Section -->
        <div class="card bg-base-100 shadow-xl form-section">
            <div class="card-body">
                <h2 class="card-title text-xl flex items-center gap-2">
                    <span class="iconify w-5 h-5" data-icon="solar:settings-bold-duotone"></span> Additional Settings
                </h2>
                <p class="text-sm text-base-content/70 mb-4">Configure additional project settings</p>
                <div class="form-control w-full mt-4">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox" wire:model="send_notifications" class="checkbox checkbox-primary" />
                        <span class="label-text">Send notifications to team members about the updates</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex justify-end gap-4 sticky bottom-0 bg-base-200 p-4 shadow-lg rounded-t-lg">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Project</button>
        </div>
    </form>
</div>
