<div class="min-h-screen bg-base-200/50">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold">Create Daily Report</h2>
                <p class="text-base-content/70 mt-1">Record your daily progress</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-ghost btn-sm">
                <span class="iconify w-5 h-5 mr-1" data-icon="solar:arrow-left-bold-duotone"></span>
                Back to Reports
            </a>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Date and Summary Card -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <!-- Project Selection -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-medium">Project</span>
                            </label>
                            <select wire:model="project_id" class="select select-bordered w-full" required>
                                <option value="">Select a project</option>
                                @foreach($availableProjects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date Input -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text font-medium">Date</span>
                            </label>
                            <input type="date" 
                                wire:model="date" 
                                class="input input-bordered w-full cursor-not-allowed opacity-70" 
                                disabled 
                                required />
                            @error('date') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Summary -->
                        <div class="form-control w-full mt-4 flex flex-col gap-2">
                            <label class="label">
                                <span class="label-text font-medium">Daily Summary</span>
                                <span class="label-text-alt text-base-content/70">Required</span>
                            </label>
                            <textarea wire:model="summary" 
                                    class="textarea textarea-bordered min-h-32 w-full"  
                                    placeholder="Write a summary of your day's work and achievements..." required></textarea>
                            @error('summary') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-8">
                <button type="submit" class="btn btn-primary">
                    <span class="iconify w-5 h-5 mr-1" data-icon="solar:check-circle-bold-duotone"></span>
                    Submit Daily Report
                </button>
            </div>
        </form>
</div> 