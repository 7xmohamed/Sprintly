<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-base-200 opacity-40" aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="relative w-full max-w-xl p-6 my-8 overflow-hidden text-left transition-all transform bg-base-100 rounded-lg shadow-xl">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg">Add Project Members</h3>
                        <button wire:click="close" class="btn btn-sm btn-circle btn-ghost text-xl">
                            <span class="iconify w-8 h-8" data-icon="solar:close-circle-line-duotone"></span>
                        </button>
                    </div>

                    @if($availableMembers->isEmpty())
                        <div class="text-center py-6">
                            <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center mb-4">
                                <span class="iconify w-8 h-8" data-icon="solar:users-group-two-rounded-bold-duotone"></span>
                            </div>
                            <h3 class="font-semibold">No Available Members</h3>
                            <p class="text-base-content/70 mt-1">No available members to add from this department.</p>
                        </div>
                    @else
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                            @foreach($availableMembers as $member)
                                <label class="flex items-center gap-4 p-3 rounded-lg hover:bg-base-200 cursor-pointer">
                                    <input type="checkbox" 
                                           wire:model.defer="selectedMembers" 
                                           value="{{ $member->id }}" 
                                           class="checkbox checkbox-primary">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="avatar">
                                            <div class="w-10 rounded-full">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $member->name }}</div>
                                            <div class="text-sm text-base-content/70">{{ $member->email }}</div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-2 mt-6">
                            <button wire:click="close" class="btn">Cancel</button>
                            <button wire:click="addMembers" class="btn btn-primary">Add Selected Members</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
