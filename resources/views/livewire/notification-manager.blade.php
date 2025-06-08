<div class="fixed top-4 right-4 z-50 space-y-2">
    @foreach($notifications as $notification)
        <div 
            x-data="{
                show: true,
                init() {
                    setTimeout(() => { this.show = false }, 3000);
                }
            }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-40"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-40"
            x-on:transitionend.leave="$wire.removeNotification('{{ $notification['id'] }}')"
            class="alert {{ $notification['type'] === 'success' ? 'alert-success' : ($notification['type'] === 'error' ? 'alert-error' : 'alert-info') }} shadow-lg w-80 rounded-lg"
        >
            <div class="flex items-center">
                @if($notification['type'] === 'success')
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @elseif($notification['type'] === 'error')
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @endif
                <span>{{ $notification['message'] }}</span>
            </div>
            <button class="btn btn-ghost btn-xs" x-on:click="show = false">×</button>
        </div>
    @endforeach
</div> 