<div>
    <div class="modal-header mb-4">
        <h2 class="card-title text-xl flex items-center gap-2">
            <span class="iconify w-6 h-6 text-primary" data-icon="solar:pen-new-square-bold-duotone"></span> Edit Task
        </h2>
    </div>
    
    <form wire:submit.prevent="update">
        <div class="modal-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label" for="title">
                        <span class="label-text">Task Title</span>
                    </label>
                    <input type="text" id="title" class="input input-bordered w-full" wire:model="title" required />
                    @error('title') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-control">
                    <label class="label" for="project_id">
                        <span class="label-text">Project</span>
                    </label>
                    <select id="project_id" class="select select-bordered w-full" wire:model.live="project_id" required>
                        <option value="">Select a project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-control">
                    <label class="label" for="priority">
                        <span class="label-text">Priority</span>
                    </label>
                    <select id="priority" class="select select-bordered w-full" wire:model="priority">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    @error('priority') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-control">
                    <label class="label" for="current_status">
                        <span class="label-text">Status</span>
                    </label>
                    <select id="current_status" class="select select-bordered w-full" wire:model="current_status">
                        <option value="todo">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('current_status') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
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
            </div>
            
            <div class="form-control mt-4 flex flex-col gap-2">
                <label class="label" for="description">
                    <span class="label-text">Description</span>
                </label>
                <textarea id="description" class="textarea textarea-bordered h-24 w-full" wire:model="description"></textarea>
                @error('description') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
            </div>
            
            <!-- Repetitive Task Section -->
            <div class="mt-4 p-4 bg-base-200 rounded-lg">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox" class="checkbox checkbox-primary" wire:model.live="is_repetitive" />
                        <span class="label-text font-medium">Make this a repetitive task</span>
                    </label>
                </div>

                <div class="mt-4" x-data x-show="$wire.is_repetitive">
                    <div class="bg-base-100 p-4 rounded-lg">
                        <h3 class="font-bold mb-4">Repetitive Task Options</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label" for="repetition_rate">
                                    <span class="label-text">Repeat</span>
                                </label>
                                <select id="repetition_rate" class="select select-bordered w-full" wire:model.live="repetition_rate">
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
            
            <div class="form-control mt-4">
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
            
            <div class="form-control mt-4">
                <label class="label" for="status">
                    <span class="label-text">Approval Status</span>
                </label>
                <select id="status" class="select select-bordered w-full" wire:model="status">
                    <option value="pending_approval">Pending Approval</option>
                    <option value="approved">Approved</option>
                </select>
                @error('status') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="modal-footer mt-6 flex justify-end gap-2">
            <button type="button" class="btn" wire:click="$dispatch('closeModal')">
                <span class="iconify w-5 h-5 mr-2" data-icon="solar:close-circle-bold-duotone"></span>
                Cancel
            </button>
            <button type="submit" class="btn btn-primary">
                <span class="iconify w-5 h-5 mr-2" data-icon="solar:disk-bold-duotone"></span>
                Update Task
            </button>
        </div>
    </form>
</div> 