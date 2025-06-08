<div>
    <!-- Calendar Header & Controls -->
    <div class="card bg-base-100 shadow-md mb-6">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="card-title">Task Calendar</h2>
                
                <div class="join">
                    <button wire:click="previousMonth" class="btn btn-sm join-item">
                        <iconify-icon icon="lucide:chevron-left"></iconify-icon>
                    </button>
                    <button wire:click="goToToday" class="btn btn-sm join-item btn-primary">Today</button>
                    <button wire:click="nextMonth" class="btn btn-sm join-item">
                        <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Project</span>
                    </label>
                    <select class="select select-bordered w-full" wire:model.live="projectFilter">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-control flex items-end">
                    <label class="label cursor-pointer justify-start">
                        <input type="checkbox" class="checkbox checkbox-primary mr-2" wire:model.live="repetitiveOnly" />
                        <span class="label-text">Show Repetitive Tasks Only</span>
                    </label>
                </div>
                
                <div class="form-control flex items-end">
                    <label class="label cursor-pointer justify-start">
                        <input type="checkbox" class="checkbox checkbox-primary mr-2" wire:model.live="showHolidays" />
                        <span class="label-text">Show Moroccan Holidays</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Month Calendar -->
    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            <h3 class="text-xl font-bold mb-4 text-center">{{ $monthName }}</h3>
            
            <div class="grid grid-cols-7 gap-px bg-base-300 rounded-lg overflow-hidden">
                <!-- Calendar Header (Day Names) -->
                <div class="bg-base-200 p-2 text-center font-medium">Sun</div>
                <div class="bg-base-200 p-2 text-center font-medium">Mon</div>
                <div class="bg-base-200 p-2 text-center font-medium">Tue</div>
                <div class="bg-base-200 p-2 text-center font-medium">Wed</div>
                <div class="bg-base-200 p-2 text-center font-medium">Thu</div>
                <div class="bg-base-200 p-2 text-center font-medium">Fri</div>
                <div class="bg-base-200 p-2 text-center font-medium">Sat</div>
                
                <!-- Calendar Days -->
                @foreach ($weeks as $week)
                    @foreach ($week as $day)
                        <div class="bg-base-100 min-h-[120px] p-2 relative 
                            {{ !$day['isCurrentMonth'] ? 'opacity-40' : '' }}
                            {{ $day['isToday'] ? 'ring-2 ring-primary ring-inset' : '' }}
                            {{ $day['isHoliday'] ? 'bg-error/10 holiday-cell' : '' }}">
                            
                            <!-- Date Number -->
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium {{ $day['isToday'] ? 'bg-primary text-primary-content rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                                    {{ $day['date']->format('j') }}
                                </span>
                                
                                @if(!$day['isHoliday'])
                                    <a href="{{ url('/tasks') }}?new_task=true&due_date={{ $day['date']->format('Y-m-d') }}" 
                                        class="btn btn-xs btn-ghost btn-circle">
                                        <iconify-icon icon="lucide:plus"></iconify-icon>
                                    </a>
                                @endif
                            </div>
                            
                            <!-- Holiday Indicator -->
                            @if($day['isHoliday'])
                                <div class="mb-1 p-2 rounded border-l-4 border-error bg-gradient-to-r from-error/20 to-error/5 shadow-sm">
                                    <div class="text-xs font-bold text-error flex items-center gap-1 mb-1">
                                    <iconify-icon icon="lucide:calendar-off"></iconify-icon>
                                        <span>NATIONAL HOLIDAY</span>
                                    </div>
                                    <div class="text-sm text-error/90 font-medium truncate">
                                        {{ $day['holidayName'] }}
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Regular Tasks -->
                            @foreach ($day['tasks'] as $task)
                                <div wire:click="openTaskModal({{ $task->id }})" 
                                    class="cursor-pointer mb-1 p-1 text-xs rounded truncate
                                    {{ $task->priority === 'high' ? 'bg-error/20 text-error' : '' }}
                                    {{ $task->priority === 'medium' ? 'bg-warning/20 text-warning-content' : '' }}
                                    {{ $task->priority === 'low' ? 'bg-info/20 text-info-content' : '' }}
                                    {{ $day['isHoliday'] ? 'opacity-50' : '' }}">
                                    {{ $task->title }}
                                </div>
                            @endforeach
                            
                            <!-- Repetitive Tasks -->
                            @foreach ($day['repetitiveTasks'] as $task)
                                <div wire:click="openTaskModal({{ $task->id }})" 
                                    class="cursor-pointer mb-1 p-1 text-xs rounded truncate border-l-4 border-accent
                                    {{ $task->priority === 'high' ? 'bg-error/20 text-error' : '' }}
                                    {{ $task->priority === 'medium' ? 'bg-warning/20 text-warning-content' : '' }}
                                    {{ $task->priority === 'low' ? 'bg-info/20 text-info-content' : '' }}
                                    {{ $day['isHoliday'] ? 'opacity-50' : '' }}">
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex items-center gap-1 truncate">
                                            <iconify-icon icon="lucide:repeat"></iconify-icon>
                                            <span class="truncate">{{ $task->title }}</span>
                                        </div>
                                        <span class="badge badge-xs badge-accent shrink-0" title="Repeats {{ $task->repetitiveTask->repetition_rate }}">
                                            {{ ucfirst(substr($task->repetitiveTask->repetition_rate, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endforeach
            </div>
            
            <!-- Holiday Legend -->
            @if($showHolidays)
            <div class="mt-4 p-3 bg-base-200 rounded-lg">
                <h4 class="font-bold text-sm mb-2">Moroccan Holidays Legend:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-error/10 border-l-4 border-error rounded"></div>
                        <span class="text-xs">National Holiday</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-error">
                        <iconify-icon icon="lucide:calendar-off"></iconify-icon>
                        <span>No tasks can be registered on holidays</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .holiday-cell {
            position: relative;
            overflow: hidden;
        }
        
        .holiday-cell::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(to right, theme('colors.error'), transparent);
            z-index: 1;
        }
    </style>
</div> 