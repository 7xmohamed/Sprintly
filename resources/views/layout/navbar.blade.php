<nav class="bg-base-100 border-b border-base-200 fixed right-0 top-0 transition-all duration-300 left-64 z-[40]" id="main-navbar">
    <div class="px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Left side -->
            <div class="flex items-center space-x-4">
                <!-- Sidebar Toggle -->
                <button class="btn btn-ghost btn-sm rounded-lg" aria-label="Toggle sidebar">
                    <span class="iconify w-5 h-5" data-icon="solar:hamburger-menu-broken"></span>
                </button>
                
                <!-- Search
                <div class="hidden sm:block">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="iconify w-4 h-4 text-base-content/60" data-icon="solar:magnifer-bold-duotone"></span>
                        </span>
                        <input type="text" class="input input-bordered w-72 pl-10 pr-4 py-2 text-sm" placeholder="Search...">
                    </div>
                </div> -->
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" class="btn btn-ghost btn-sm rounded-lg" aria-label="Theme toggle">
                    <span class="iconify w-5 h-5 dark:hidden" data-icon="solar:sun-bold-duotone"></span>
                    <span class="iconify w-5 h-5 hidden dark:inline" data-icon="solar:moon-bold-duotone"></span>
                </button>

                <!-- Notifications -->
                <details class="dropdown dropdown-end">
                    <summary class="btn btn-ghost btn-sm rounded-lg" aria-label="Notifications">
                        <span class="iconify w-5 h-5" data-icon="solar:bell-bold-duotone"></span>
                        <span class="badge badge-sm badge-primary badge-pill absolute -top-1 -right-1">{{ \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count() }}</span>
                    </summary>
                    <div class="dropdown-content bg-base-100 rounded-box shadow-lg mt-2 w-80 z-[50]">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h6 class="text-sm font-medium">Notifications</h6>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-primary hover:underline">View All</a>
                            </div>
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                                @forelse(\App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->with('from')->latest()->take(5)->get() as $notification)
                                    <div class="flex items-start space-x-3 p-3 rounded-lg {{ !$notification->is_read ? 'bg-base-200' : '' }}">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-primary text-primary-content flex items-center justify-center">
                                                <span class="iconify w-5 h-5" data-icon="solar:notification-unread-bold-duotone"></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-base-content font-medium">{{ $notification->title }}</p>
                                            <p class="text-sm text-base-content/70">{{ $notification->message }}</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <p class="text-xs text-base-content/60">From: {{ $notification->from->name }}</p>
                                                <p class="text-xs text-base-content/60">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        @if(!$notification->is_read)
                                            <form method="POST" action="{{ route('notifications.markAsRead', $notification) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-xs">
                                                    <span class="iconify w-4 h-4" data-icon="solar:check-circle-bold-duotone"></span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center mb-4 mx-auto">
                                            <span class="iconify w-8 h-8" data-icon="solar:bell-off-bold-duotone"></span>
                                        </div>
                                        <p class="text-base-content/70">No notifications</p>
                                    </div>
                                @endforelse
                            </div>
                            @if(\App\Models\Notification::where('user_id', auth()->id())->count() > 0)
                                <div class="mt-4 pt-4 border-t border-base-200">
                                    <a href="{{ route('notifications.index') }}" class="btn btn-primary btn-sm w-full">View All Notifications</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </details>

                <!-- User Menu -->
                <details class="dropdown dropdown-end">
                    <summary class="flex items-center space-x-3 focus:outline-none cursor-pointer">
                        <img class="h-8 w-8 rounded-full border border-base-200" src="{{ asset('images/image.png') }}" alt="User avatar">
                        <div class="hidden md:block text-left">
                            <h6 class="text-sm font-medium">{{ auth()->user()->name }}</h6>
                            <p class="text-xs text-base-content/60">{{ auth()->user()->role }}</p>
                        </div>
                        <span class="iconify w-4 h-4 text-base-content/60" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                    </summary>
                    <div class="dropdown-content bg-base-100 rounded-box shadow-lg mt-2 w-48 z-[50]">
                        <ul class="menu menu-sm w-full">
                            <li>
                                <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2">
                                    <span class="iconify w-4 h-4 mr-2" data-icon="solar:user-bold-duotone"></span>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li>
                                <a href="/pages/settings" class="flex items-center px-4 py-2">
                                    <span class="iconify w-4 h-4 mr-2" data-icon="solar:settings-bold-duotone"></span>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="w-full flex p-0">
                                    @csrf
                                    <button type="submit" class="flex items-center px-4 py-2 w-full text-error hover:bg-error/10 gap-2">
                                        <span class="iconify w-4 h-4 mr-2" data-icon="solar:logout-3-bold-duotone"></span>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>
    </div>
</nav>