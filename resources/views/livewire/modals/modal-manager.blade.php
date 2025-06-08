<div>
    <style>
        /* For Webkit browsers (Chrome, Safari) */
        .max-h-\[80vh\]::-webkit-scrollbar {
            width: 8px;
        }
        
        .max-h-\[80vh\]::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .max-h-\[80vh\]::-webkit-scrollbar-thumb {
            background-color: hsl(var(--p) / 0.5);
            border-radius: 10px;
        }
        
        .max-h-\[80vh\]::-webkit-scrollbar-thumb:hover {
            background-color: hsl(var(--p) / 0.8);
        }
    </style>

    @if ($show)
        <div
            x-data="{ show: @entangle('show') }"
            x-show="show"
            x-on:keydown.escape.window="show = false"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto py-10"
            style="display: none;">
            
            <!-- Modal backdrop -->
            <div 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50"
                x-on:click="show = false">
            </div>
            
            <!-- Modal content container -->
            <div 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative w-11/12 max-w-4xl bg-base-100 rounded-lg shadow-xl"
                @click.away="show = false">
                
                <!-- Close button -->
                <button 
                    x-on:click="show = false"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-500 z-20">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Scrollable content area -->
                <div class="max-h-[80vh] overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: hsl(var(--p)) transparent;">
                    <div class="p-6">
                    @if ($component)
                        @livewire($component, $arguments, key($component . '-' . rand()))
                    @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Debug modal events
            Livewire.on('openModal', (params) => {
                console.log('Modal open requested with:', params);
            });
        });
    </script>
</div> 