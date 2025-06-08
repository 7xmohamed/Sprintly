<div>
    <div class="dropdown dropdown-end">
        <button class="btn btn-ghost btn-sm rounded-lg" aria-label="Notifications">
            <span class="iconify w-5 h-5" data-icon="solar:bell-bold-duotone"></span>
            @if($unreadCount > 0)
                <span class="badge badge-sm badge-primary badge-pill absolute -top-1 -right-1">{{ $unreadCount }}</span>
            @endif
        </button>
        <div class="dropdown-content bg-base-100 rounded-box shadow-lg mt-2 w-80 z-[50]">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="text-sm font-medium">Unread Notifications</h6>
                    <div class="flex items-center gap-2">
                        @if($notifications->count() > 0)
                            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="text-xs text-primary hover:underline">View All Unread</a>
                        @endif
                        <a href="{{ route('notifications.index') }}" class="text-xs text-base-content/70 hover:underline">View All</a>
                    </div>
                </div>
                <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                    @forelse($notifications as $notification)
                        <div class="flex items-start space-x-3 p-3 rounded-lg bg-base-200 border-l-2 border-primary">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="iconify w-5 h-5" data-icon="{{ 
                                        $notification->type === 'assignment' ? 'solar:clipboard-list-bold-duotone' : 
                                        ($notification->type === 'reminder' ? 'solar:bell-bold-duotone' : 
                                        ($notification->type === 'status_change' ? 'solar:refresh-circle-bold-duotone' : 
                                        ($notification->type === 'mention' ? 'solar:user-speak-bold-duotone' : 'solar:notification-unread-bold-duotone'))) 
                                    }}"></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium">{{ $notification->title }}</p>
                                    <button wire:click="markAsRead({{ $notification->id }})" class="btn btn-ghost btn-xs" title="Mark as read">
                                        <span class="iconify w-4 h-4" data-icon="solar:check-circle-bold-duotone"></span>
                                    </button>
                                </div>
                                <p class="text-sm text-base-content/70 line-clamp-2">{{ $notification->message }}</p>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-xs text-base-content/60">From: {{ $notification->from->name }}</p>
                                    <p class="text-xs text-base-content/60" title="{{ $notification->created_at->format('M d, Y H:i') }}">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div class="w-12 h-12 bg-base-200 text-base-content/50 rounded-full inline-flex items-center justify-center mb-2 mx-auto">
                                <span class="iconify w-6 h-6" data-icon="solar:bell-off-bold-duotone"></span>
                            </div>
                            <p class="text-sm text-base-content/70">No unread notifications</p>
                        </div>
                    @endforelse
                </div>
                @if($notifications->count() > 0)
                    <div class="mt-4 pt-4 border-t border-base-200">
                        <div class="flex gap-2">
                            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn btn-primary btn-sm flex-1">
                                View All Unread
                            </a>
                            <a href="{{ route('notifications.index') }}" class="btn btn-ghost btn-sm flex-1">
                                View All
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 