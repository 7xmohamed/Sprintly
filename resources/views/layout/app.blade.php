<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    {{-- Load assets using Laravel Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>

<body class="font-sans antialiased bg-base-100/50">
    <div class="min-h-screen">
        @include('layout.sidebar')
        @include('layout.navbar')

        <!-- Flash Messages -->
        @if (session()->has('success') || session()->has('error'))
            <div class="fixed top-4 right-4 z-50 animate-fade-in-down">
                <div class="rounded-md p-4 {{ session()->has('success') ? 'bg-green-50' : 'bg-red-50' }} shadow-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if(session()->has('success'))
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="{{ session()->has('success') ? 'text-green-800' : 'text-red-800' }}">
                                {{ session()->get('success') ?? session()->get('error') }}
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                                    class="{{ session()->has('success') ? 'text-green-500 hover:text-green-600' : 'text-red-500 hover:text-red-600' }} rounded-md p-1.5">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="pl-64 pt-16 transition-all duration-300">
            <div class="container mx-auto px-4 py-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Set initial theme from localStorage or system preference
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }

            // Auto-hide flash messages after 5 seconds
            setTimeout(() => {
                const flashMessage = document.querySelector('.animate-fade-in-down');
                if (flashMessage) {
                    flashMessage.remove();
                }
            }, 5000);
        });

        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.querySelector('#layout-sidebar');
            const navbar = document.querySelector('#main-navbar');
            const main = document.querySelector('main');
            const sidebarToggles = document.querySelectorAll('[aria-label="Toggle sidebar"]');
            
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                main.classList.toggle('pl-64');
                main.classList.toggle('pl-0');
                navbar.classList.toggle('left-64');
                navbar.classList.toggle('left-0');
            }

            sidebarToggles.forEach(toggle => {
                toggle.addEventListener('click', toggleSidebar);
            });

            // Handle responsive behavior
            const mediaQuery = window.matchMedia('(max-width: 1024px)');
            function handleResponsive(e) {
                if (e.matches) {
                    sidebar.classList.add('-translate-x-full');
                    main.classList.remove('pl-64');
                    main.classList.add('pl-0');
                    navbar.classList.remove('left-64');
                    navbar.classList.add('left-0');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    main.classList.add('pl-64');
                    main.classList.remove('pl-0');
                    navbar.classList.add('left-64');
                    navbar.classList.remove('left-0');
                }
            }
            
            mediaQuery.addListener(handleResponsive);
            handleResponsive(mediaQuery);
        });
    </script>
    
    <livewire:notification-manager />
    @stack('scripts')
</body>

</html>
