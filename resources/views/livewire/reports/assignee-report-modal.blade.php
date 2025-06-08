<div class="w-full">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <div class="avatar">
                <div class="w-16 rounded-full">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}" 
                         alt="{{ $user->first_name }} {{ $user->last_name }}" />
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $user->first_name }} {{ $user->last_name }}</h2>
                <p class="text-base-content/70">{{ $user->departments->first()?->name ?? 'No Department' }}</p>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="mb-6">
        <div class="form-control w-full max-w-xs">
            <label class="label">
                <span class="label-text">Date Range</span>
            </label>
            <select class="select select-bordered w-full" wire:model.live="dateRange" wire:change="setDateRange($event.target.value)">
                @foreach($dateRangeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Reports List -->
    <div class="space-y-6">
        @forelse($reports as $report)
            <div class="card bg-base-200">
                <div class="card-body p-4">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-semibold">{{ \Carbon\Carbon::parse($report['date'])->format('F j, Y g:i A') }}</h3>
                            <p class="text-sm text-base-content/70">
                                Submitted {{ \Carbon\Carbon::parse($report['submitted_at'])->diffForHumans() }}
                            </p>
                        </div>
                        <div>
                            <span class="badge badge-primary">{{ $report['project']['name'] }}</span>
                        </div>
                    </div>

                    @if($report['summary'])
                        <div>
                            <h4 class="font-medium mb-2">Daily Summary</h4>
                            <p class="text-base-content/80">{{ $report['summary'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-base-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="iconify w-8 h-8" data-icon="solar:notebook-bold-duotone"></span>
                </div>
                <h3 class="font-semibold">No Reports Found</h3>
                <p class="text-base-content/70 mt-1">No reports available for the selected date range</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Footer -->
    <div class="modal-action">
        <button class="btn btn-ghost" wire:click="$parent.closeAssigneeModal">Close</button>
    </div>
</div> 