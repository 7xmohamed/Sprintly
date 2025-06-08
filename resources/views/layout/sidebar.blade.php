<div id="layout-sidebar" class="bg-base-100 border-r border-base-200 flex flex-col h-screen w-64 fixed left-0 top-0">
    <div class="p-4 border-b border-base-200">
        <a class="flex items-center justify-center" href="/" data-discover="true">
            <img alt="logo-dark" class="hidden h-8 dark:inline" src="/images/logo/logo-dark.svg" />
            <img alt="logo-light" class="h-8 dark:hidden" src="/images/logo/logo-light.svg" />
        </a>
    </div>

    <div class="overflow-y-auto flex-1 py-4">
        <nav class="px-4 space-y-4">
            <div class="mb-4">
                <p class="text-xs font-semibold text-base-content/60 uppercase tracking-wider mb-4 px-3">Main Menu</p>
                <div class="space-y-1">
                    <a href="/dashboard" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-base-200 transition-colors group {{ request()->is('dashboard') ? 'bg-primary/10 text-primary' : 'text-base-content/80' }}">
                        <span class="iconify w-5 h-5 mr-3" data-icon="solar:home-2-bold-duotone"></span>
                        <span>Dashboard</span>
                    </a>
                    <a href="/projects" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-base-200 transition-colors group {{ request()->is('projects') ? 'bg-primary/10 text-primary' : 'text-base-content/80' }}">
                        <span class="iconify w-5 h-5 mr-3" data-icon="solar:chart-2-bold-duotone"></span>
                        <span>Projects</span>
                    </a>
                    <a href="/tasks" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-base-200 transition-colors group {{ request()->is('tasks') ? 'bg-primary/10 text-primary' : 'text-base-content/80' }}">
                        <span class="iconify w-5 h-5 mr-3" data-icon="solar:list-bold-duotone"></span>
                        <span>Tasks</span>
                    </a>
                    <a href="/calendar" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-base-200 transition-colors group {{ request()->is('calendar') ? 'bg-primary/10 text-primary' : 'text-base-content/80' }}">
                        <span class="iconify w-5 h-5 mr-3" data-icon="solar:calendar-bold-duotone"></span>
                        <span>Calendar</span>
                    </a>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-xs font-semibold text-base-content/60 uppercase tracking-wider mb-4 px-3">Communication</p>
                <div class="space-y-1">
                    <a href="/chats" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-base-200 transition-colors group {{ request()->is('chats') ? 'bg-primary/10 text-primary' : 'text-base-content/80' }}">
                        <span class="iconify w-5 h-5 mr-3" data-icon="solar:chat-round-dots-bold-duotone"></span>
                        <span>Chats</span>
                    </a>
                    <a href="/reports" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-base-200 transition-colors group {{ request()->is('reports') ? 'bg-primary/10 text-primary' : 'text-base-content/80' }}">
                        <span class="iconify w-5 h-5 mr-3" data-icon="solar:document-bold-duotone"></span>
                        <span>Reports</span>
                    </a>
                    <a href="/notifications" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-base-200 transition-colors group {{ request()->is('notifications') ? 'bg-primary/10 text-primary' : 'text-base-content/80' }}">
                        <span class="iconify w-5 h-5 mr-3" data-icon="solar:bell-bold-duotone"></span>
                        <span>Notifications</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <div class="p-4 border-t border-base-200">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <img class="h-8 w-8 rounded-full" src="{{ asset('images/image.png') }}" alt="User avatar" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-base-content truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-base-content/60 truncate">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>
</div>
