<div class="w-full">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Edit Report</h2>
        <button wire:click="close" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
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
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium">Date</span>
            </label>
            <input type="date" wire:model="date" class="input input-bordered w-full" required />
            @error('date') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Summary -->
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium">Daily Summary</span>
                <span class="label-text-alt text-base-content/70">Required</span>
            </label>
            <textarea wire:model="summary" 
                    class="textarea textarea-bordered min-h-32 w-full"  
                    placeholder="Write a summary of your day's work and achievements..." required></textarea>
            @error('summary') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mt-8">
            <button type="submit" class="btn btn-primary">
                <span class="iconify w-5 h-5 mr-1" data-icon="solar:check-circle-bold-duotone"></span>
                Update Report
            </button>
        </div>
    </form>
</div> 