<div>
    <style>
        /* Required field styling */
        .required-field::after {
            content: '*';
            color: hsl(var(--er));
            margin-left: 0.25rem;
        }
        
        /* Form section styling */
        .form-section {
            transition: all 0.3s ease-in-out;
        }
        
        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
    
    <form wire:submit="create" id="create-task-form" class="space-y-6 max-w-5xl mx-auto">
        <!-- Basic Information Section -->
        <div class="card bg-base-100 shadow-xl form-section">
            <div class="card-body">
                <h2 class="card-title text-xl flex items-center gap-2">
                    <span class="iconify w-6 h-6 text-primary" data-icon="solar:info-circle-bold-duotone"></span> Task Information
                </h2>
                <p class="text-sm text-base-content/70 mb-4">Enter the essential details about your task</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Task Title</span>
                        </label>
                        <input type="text" wire:model="title" placeholder="Enter task title" class="input input-bordered w-full" required />
                        @error('title') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Project</span>
                        </label>
                        <select wire:model.live="project_id" class="select select-bordered w-full" required>
                            <option value="">Select project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control">
                        <label class="label" for="start_date">
                            <span class="label-text font-medium">Start Date</span>
                        </label>
                        <input type="date" id="start_date" class="input input-bordered w-full focus:input-primary" 
                               wire:model="start_date" 
                               min="{{ date('Y-m-d') }}" />
                        @error('start_date') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control">
                        <label class="label" for="due_date">
                            <span class="label-text font-medium">Due Date</span>
                        </label>
                        <input type="date" id="due_date" class="input input-bordered w-full focus:input-primary" 
                               wire:model="due_date" 
                               min="{{ date('Y-m-d') }}" />
                        @error('due_date') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Priority</span>
                        </label>
                        <select wire:model="priority" class="select select-bordered w-full" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('priority') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text required-field">Status</span>
                        </label>
                        <select wire:model="current_status" class="select select-bordered w-full" required>
                            <option value="todo">Not Started</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        @error('current_status') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="form-control w-full mt-4">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea wire:model="description" class="textarea textarea-bordered h-32 w-full" placeholder="Provide a detailed description of the task objectives, requirements and expectations"></textarea>
                    @error('description') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Team Assignment Section -->
        <div class="card bg-base-100 shadow-xl form-section">
            <div class="card-body">
                <h2 class="card-title text-xl flex items-center gap-2">
                    <span class="iconify w-6 h-6 text-primary" data-icon="solar:users-group-rounded-bold-duotone"></span> Task Assignment
                </h2>
                <p class="text-sm text-base-content/70 mb-4">Assign team members to this task</p>
                
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Assign To</span>
                    </label>
                    <div class="bg-base-200 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @if($projectMembers && count($projectMembers) > 0)
                                @foreach($projectMembers as $member)
                                    <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-base-300 rounded-md">
                                        <input type="checkbox" wire:model.live="assignees" value="{{ $member->id }}" class="checkbox checkbox-sm" />
                                        <div class="flex items-center gap-2">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content w-8 rounded-full">
                                                    <span>{{ substr($member->first_name ?? '', 0, 1) }}{{ substr($member->last_name ?? '', 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                                                <span class="text-sm text-gray-500 block">{{ $member->role }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            @elseif($project_id)
                                <div class="col-span-full text-center py-4 text-gray-500">
                                    No team members found for this project
                                </div>
                            @else
                                <div class="col-span-full text-center py-4 text-gray-500">
                                    Please select a project to see available team members
                                </div>
                            @endif
                        </div>
                    </div>
                    @error('assignees') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Repetitive Task Section -->
        <div class="card bg-base-100 shadow-xl form-section">
            <div class="card-body">
                <h2 class="card-title text-xl flex items-center gap-2">
                    <span class="iconify w-6 h-6 text-primary" data-icon="solar:repeat-bold-duotone"></span> Recurrence Settings
                </h2>
                <p class="text-sm text-base-content/70 mb-4">Configure if this task should repeat on a schedule</p>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox" wire:model.live="is_repetitive" class="checkbox checkbox-primary" />
                        <span class="label-text">Make this a repetitive task</span>
                    </label>
                </div>
                
                <div class="mt-4" x-data x-show="$wire.is_repetitive">
                    <div class="bg-base-200 p-4 rounded-lg">
                        <h3 class="font-bold mb-4">Repetitive Task Options</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Repeat</span>
                                </label>
                                <select wire:model.live="repetition_rate" class="select select-bordered w-full">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                @error('repetition_rate') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-control">
                                <label class="label" for="recurrence_end_date">
                                    <span class="label-text">Until (optional)</span>
                                </label>
                                <input type="date" id="recurrence_end_date" class="input input-bordered w-full" 
                                       wire:model="recurrence_end_date" 
                                       min="{{ date('Y-m-d') }}" />
                                @error('recurrence_end_date') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        @if($repetition_rate === 'weekly')
                            <div class="form-control mt-4">
                                <label class="label">
                                    <span class="label-text">Repeat on</span>
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="checkbox checkbox-sm" 
                                                wire:model.live="recurrence_days" 
                                                value="{{ $index }}" />
                                            <span>{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('recurrence_days') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @if($repetition_rate === 'monthly')
                            <div class="form-control mt-4">
                                <label class="label">
                                    <span class="label-text">Day of month</span>
                                </label>
                                <input type="number" class="input input-bordered w-full" 
                                    wire:model.live="recurrence_month_day" 
                                    min="1" max="31" />
                                @error('recurrence_month_day') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @if($repetition_rate === 'yearly')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Month</span>
                                    </label>
                                    <select class="select select-bordered w-full" wire:model.live="recurrence_month">
                                        @foreach(range(1, 12) as $month)
                                            <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('recurrence_month') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Day of month</span>
                                    </label>
                                    <input type="number" class="input input-bordered w-full" 
                                        wire:model.live="recurrence_month_day" 
                                        min="1" max="31" />
                                    @error('recurrence_month_day') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex justify-end gap-4 sticky bottom-0 bg-base-200 p-4 shadow-lg rounded-t-lg">
            <a href="{{ route('tasks.index') }}" class="btn btn-outline">
                <span class="iconify w-5 h-5 mr-2" data-icon="solar:close-circle-bold-duotone"></span>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <span class="iconify w-5 h-5 mr-2" data-icon="solar:disk-bold-duotone"></span>
                Create Task
            </button>
        </div>
    </form>
</div> 