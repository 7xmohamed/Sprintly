<div>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <h2 class="text-2xl font-semibold">Notifications</h2>
            @if($notifications->count() > 0)
                <button wire:click="markAllAsRead" class="btn btn-ghost btn-sm normal-case">
                    <span class="iconify w-5 h-5 mr-1" data-icon="solar:check-circle-bold-duotone"></span>
                    Mark all as read
                </button>
            @endif
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="join">
                <input type="text" wire:model.live="search" placeholder="Search notifications..." class="input input-bordered join-item w-full sm:w-64" />
                <select wire:model.live="filter" class="select select-bordered join-item">
                    <option value="all">All notifications</option>
                    <option value="unread">Unread only</option>
                </select>
            </div>
        </div>
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="card bg-base-200 {{ !$notification->is_read ? 'border-l-4 border-primary' : '' }}">
                    <div class="card-body p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-medium">{{ $notification->title }}</h3>
                                    <span class="badge badge-sm {{ 
                                        $notification->type === 'assignment' ? 'badge-primary' : 
                                        ($notification->type === 'reminder' ? 'badge-warning' : 
                                        ($notification->type === 'status_change' ? 'badge-info' : 
                                        ($notification->type === 'mention' ? 'badge-secondary' : 'badge-accent'))) 
                                    }}">
                                        {{ str_replace('_', ' ', ucfirst($notification->type)) }}
                                    </span>
                                    @if(!$notification->is_read)
                                        <span class="badge badge-sm badge-primary">New</span>
                                    @endif
                                </div>
                                <p class="text-base-content/70 mt-1">{{ $notification->message }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-base-content/50">
                                    <span>From: {{ $notification->from->name }}</span>
                                    <span title="{{ $notification->created_at->format('M d, Y H:i') }}">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if(!$notification->is_read)
                                    <button wire:click="markAsRead({{ $notification->id }})" class="btn btn-ghost btn-sm" title="Mark as read">
                                        <span class="iconify w-5 h-5" data-icon="solar:check-circle-bold-duotone"></span>
                                    </button>
                                @endif
                                <button wire:click="deleteNotification({{ $notification->id }})" class="btn btn-ghost btn-sm" title="Delete">
                                    <span class="iconify w-5 h-5 text-error" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="card bg-base-200">
            <div class="card-body items-center text-center py-12">
                <div class="w-16 h-16 bg-base-300 rounded-full flex items-center justify-center mb-4">
                    <span class="iconify w-8 h-8 text-base-content/70" data-icon="solar:bell-off-bold-duotone"></span>
                </div>
                <h3 class="card-title">No Notifications Found</h3>
                <p class="text-base-content/70">
                    @if($filter === 'unread')
                        You have no unread notifications
                    @else
                        You don't have any notifications yet
                    @endif
                </p>
            </div>
        </div>
    @endif
</div> 