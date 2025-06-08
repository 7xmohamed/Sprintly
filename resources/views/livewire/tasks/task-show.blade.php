<div class="min-h-[50vh]">
    <!-- Modal Header -->
    <div class="modal-header border-b border-base-200 pb-4 mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-2xl font-bold">{{ $task->title }}</h3>
            <span class="badge {{ $task->status === 'approved' ? 'badge-success' : 'badge-warning' }} badge-lg">
                {{ $task->status === 'approved' ? 'Approved' : 'Pending Approval' }}
            </span>
        </div>
        <div class="flex items-center gap-3 mt-2 text-base-content/70">
            <div class="flex items-center gap-2">
                <div class="avatar">
                    <div class="w-6 rounded-full">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($task->createdBy?->name ?? 'User') }}&background=random" alt="{{ $task->createdBy?->name ?? 'User' }}" />
                    </div>
                </div>
                <span>{{ $task->createdBy?->name ?? 'Unknown User' }}</span>
            </div>
            <span>•</span>
            <span>{{ $task->project->name ?? 'No Project' }}</span>
        </div>
    </div>
    
    <div class="modal-body">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Task Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="prose max-w-none">
                    <h4 class="text-base font-semibold mb-2">Description</h4>
                    <p class="text-base-content/70">{{ $task->description ?: 'No description provided' }}</p>
                </div>

                <!-- Status and Priority -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-base font-semibold mb-2">Status</h4>
                        <div class="dropdown dropdown-hover">
                            @php
                                $isDirectorOrSupervisor = auth()->user()->hasRole(['director', 'supervisor']);
                                
                                if ($isDirectorOrSupervisor && $task->current_status === 'completed') {
                                    // For completed tasks, show approval options to directors and supervisors
                                    $statusClass = [
                                        'pending_approval' => 'badge-warning',
                                        'approved' => 'badge-success',
                                        'in_progress' => 'badge-primary',
                                    ][$task->status] ?? 'badge-ghost';
                                    
                                    $statusLabel = [
                                        'pending_approval' => 'Pending Approval',
                                        'approved' => 'Approved',
                                        'in_progress' => 'Return to Progress',
                                    ][$task->status] ?? $task->status;
                                } else {
                                    // For non-completed tasks or non-director/supervisor users, show current status
                                    $statusClass = [
                                        'todo' => 'badge-secondary',
                                        'in_progress' => 'badge-primary',
                                        'completed' => 'badge-success',
                                    ][$task->current_status] ?? 'badge-ghost';
                                    
                                    $statusLabel = [
                                        'todo' => 'Not Started',
                                        'in_progress' => 'In Progress',
                                        'completed' => 'Completed',
                                    ][$task->current_status] ?? $task->current_status;
                                }
                            @endphp
                            <div tabindex="0" role="button" class="badge {{ $statusClass }} badge-lg">{{ $statusLabel }}</div>
                            @if($isDirectorOrSupervisor && $task->current_status === 'completed')
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a wire:click="updateApprovalStatus('approved')" class="cursor-pointer {{ $task->status === 'approved' ? 'active' : '' }}">Approved</a></li>
                                    <li><a wire:click="updateApprovalStatus('pending_approval')" class="cursor-pointer {{ $task->status === 'pending_approval' ? 'active' : '' }}">Pending Approval</a></li>
                                    <li><a wire:click="updateApprovalStatus('in_progress')" class="cursor-pointer {{ $task->status === 'in_progress' ? 'active' : '' }}">Return to Progress</a></li>
                                </ul>
                            @else
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a wire:click="updateStatus('todo')" class="cursor-pointer {{ $task->current_status === 'todo' ? 'active' : '' }}">Not Started</a></li>
                                    <li><a wire:click="updateStatus('in_progress')" class="cursor-pointer {{ $task->current_status === 'in_progress' ? 'active' : '' }}">In Progress</a></li>
                                    <li><a wire:click="updateStatus('completed')" class="cursor-pointer {{ $task->current_status === 'completed' ? 'active' : '' }}">Completed</a></li>
                                </ul>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-base font-semibold mb-2">Priority</h4>
                        @php
                            $priorityClass = [
                                'high' => 'badge-error',
                                'medium' => 'badge-warning',
                                'low' => 'badge-info',
                            ][$task->priority] ?? 'badge-ghost';
                        @endphp
                        <span class="badge {{ $priorityClass }} badge-lg">{{ ucfirst($task->priority) }}</span>
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-base font-semibold mb-2">Start Date</h4>
                        <p class="text-base-content/70">{{ $task->start_date ? $task->start_date->format('M d, Y') : 'Not set' }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-base font-semibold mb-2">Due Date</h4>
                        <p class="text-base-content/70">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</p>
                    </div>
                </div>

                <!-- Repetition -->
                @if($task->repetitiveTask)
                    <div>
                        <h4 class="text-base font-semibold mb-2">Repetition Schedule</h4>
                        <div class="flex items-center gap-2 text-base-content/70">
                            <iconify-icon icon="lucide:repeat" class="text-accent"></iconify-icon>
                            <span class="badge badge-accent badge-lg">
                                {{ ucfirst($task->repetitiveTask->repetition_rate) }}
                            </span>
                            @if($task->repetitiveTask->repetition_rate === 'weekly')
                                <span class="text-sm">
                                    @php
                                        $days = [];
                                        $dayNames = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                                        for ($i = 0; $i < 7; $i++) {
                                            if ($task->repetitiveTask->recurrence_days & (1 << $i)) {
                                                $days[] = $dayNames[$i];
                                            }
                                        }
                                        echo implode(', ', $days);
                                    @endphp
                                </span>
                            @elseif($task->repetitiveTask->repetition_rate === 'monthly')
                                <span class="text-sm">day {{ $task->repetitiveTask->recurrence_month_day }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Assignees & Comments -->
            <div class="space-y-6">
                <!-- Assignees -->
                <div>
                    <h4 class="text-base font-semibold mb-3">Assigned To</h4>
                    @if($task->assignedUsers->count() > 0)
                        <div class="flex flex-col gap-2">
                            @foreach($task->assignedUsers as $user)
                                <div class="flex items-center p-2 bg-base-200 rounded-lg">
                                    <div class="avatar mr-2">
                                        <div class="w-6 rounded-full">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? 'User') }}&background=random" alt="{{ $user->name ?? 'User' }}" />
                                        </div>
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-base-content/70">No one assigned yet</p>
                    @endif
                </div>
                
                <!-- Comments -->
                <div>
                    <h4 class="text-base font-semibold mb-3">Comments</h4>
                    <div class="bg-base-200 rounded-lg p-4">
                        <div class="max-h-[300px] overflow-y-auto space-y-4 mb-4">
                            @forelse($task->taskComments as $comment)
                                <div class="bg-base-100 p-3 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="avatar">
                                                <div class="w-6 rounded-full">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user?->name ?? 'User') }}&background=random" alt="{{ $comment->user?->name ?? 'User' }}" />
                                                </div>
                                            </div>
                                            <span class="font-medium">{{ $comment->user?->name ?? 'Unknown User' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-base-content/70">{{ $comment->created_at->diffForHumans() }}</span>
                                            @if($comment->user_id === Auth::id())
                                                <button wire:click="deleteComment({{ $comment->id }})" class="btn btn-ghost btn-xs text-error">
                                                    <span class="iconify w-4 h-4" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-sm text-base-content/70">{{ $comment->text }}</p>
                                </div>
                            @empty
                                <p class="text-center text-base-content/70">No comments yet</p>
                            @endforelse
                        </div>
                        
                        <form wire:submit.prevent="addComment">
                            <div class="form-control">
                                <textarea class="textarea textarea-bordered h-20 bg-base-100" placeholder="Add a comment..." wire:model="comment" required></textarea>
                                @error('comment') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="iconify w-4 h-4 mr-1" data-icon="solar:chat-square-like-bold-duotone"></span>
                                    Add Comment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Footer -->
    <div class="modal-footer mt-6 pt-4 border-t border-base-200 flex justify-between">

        <button type="button" class="btn" wire:click="$dispatch('closeModal')">Close</button>
    </div>
</div> 