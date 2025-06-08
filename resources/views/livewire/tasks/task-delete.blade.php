<div>
    <!-- Confirm Delete Modal -->
    <div class="text-center">
        <div class="w-16 h-16 bg-error text-error-content rounded-full inline-flex items-center justify-center mb-4">
            <span class="iconify w-8 h-8" data-icon="solar:trash-bin-trash-bold-duotone"></span>
        </div>
        <h3 class="text-lg font-bold mb-2">Delete Task</h3>
        <p class="mb-4">Are you sure you want to delete <span class="font-semibold">{{ $taskTitle }}</span>? This action cannot be undone.</p>
    </div>
    
    <div class="flex justify-center gap-2 mt-6">
        <button type="button" class="btn" wire:click="$dispatch('closeModal')">Cancel</button>
        <button wire:click="delete" class="btn btn-error">Delete Task</button>
    </div>
</div> 