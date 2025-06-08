@extends('layout.app')

@section('title', 'Calendar')

@section('content')
<div class="card shadow-lg bg-base-100">
    <div class="card-body">
        <!-- Calendar Controls and Filters - Jira Style -->
        <div class="mb-6">
            <!-- Main controls and view options -->
            <div class="flex flex-col md:flex-row justify-between mb-4 gap-4">
                <div class="flex items-center space-x-2">
                <button id="prev-month" class="btn btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <h2 id="current-month" class="text-xl font-semibold"></h2>
                    <button id="next-month" class="btn btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button id="today-btn" class="btn btn-sm btn-primary ml-2">Today</button>
                </div>

                <div class="flex items-center space-x-2">
                    @if(auth()->user()->hasAnyRole(['supervisor', 'director']))
                    <a href="{{ url('/tasks') }}?new_task=true" class="btn btn-sm btn-primary mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="solar:add-circle-bold-duotone" class="iconify w-5 h-5 mr-2 iconify--solar"><path fill="currentColor" d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2s10 4.477 10 10" opacity=".5"></path><path fill="currentColor" d="M12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25z"></path></svg>
                        New Task
                    </a>
                    @endif
                    <div class="btn-group">
                        <button id="view-month" class="btn btn-sm btn-active">Month</button>
                        <button id="view-week" class="btn btn-sm">Week</button>
                        <button id="view-day" class="btn btn-sm">Day</button>
                    </div>
                </div>
            </div>

            <!-- Filters - Jira Style -->
            <div class="flex flex-col md:flex-row gap-3 justify-between bg-base-200 p-3 rounded-lg">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="font-medium text-sm">Filters:</span>
                    
                    <select id="project-filter" class="select select-sm select-bordered">
                        <option value="">All Projects</option>
                        <!-- Project options will be populated by JS -->
                    </select>
                    
                    <select id="priority-filter" class="select select-sm select-bordered">
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    
                    <select id="status-filter" class="select select-sm select-bordered">
                        <option value="">All Statuses</option>
                        <option value="todo">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="pending_approval">Pending Approval</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <div class="form-control ml-2">
                        <label class="cursor-pointer label p-0">
                            <input type="checkbox" id="show-holidays" class="checkbox checkbox-sm checkbox-secondary" checked />
                            <span class="label-text ml-2">Show Moroccan Holidays</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar Grid -->
        <div class="overflow-x-auto">
            <div class="calendar-container">
                <div id="calendar-header" class="grid grid-cols-7 gap-px mb-px bg-base-300">
                    <div class="text-center font-medium p-2 bg-base-100">Sun</div>
                    <div class="text-center font-medium p-2 bg-base-100">Mon</div>
                    <div class="text-center font-medium p-2 bg-base-100">Tue</div>
                    <div class="text-center font-medium p-2 bg-base-100">Wed</div>
                    <div class="text-center font-medium p-2 bg-base-100">Thu</div>
                    <div class="text-center font-medium p-2 bg-base-100">Fri</div>
                    <div class="text-center font-medium p-2 bg-base-100">Sat</div>
                </div>
                <div id="calendar-days" class="grid grid-cols-7 gap-px bg-base-300">
                    <!-- Calendar days will be inserted here via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<dialog id="task-modal" class="modal">
    <div class="modal-box max-w-4xl  bg-base-100 shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-2xl text-primary" id="modal-date"></h3>
            <form method="dialog">
                <button class="btn btn-circle btn-ghost">
                    <iconify-icon icon="solar:close-circle-bold-duotone" class="h-6 w-6"></iconify-icon>
                </button>
            </form>
        </div>
        
        <div class="divider"></div>
        
        <div id="tasks-container">
            <!-- Holiday Notice -->
            <div id="holiday-notice" class="mb-4 p-3 bg-error/10 border-l-4 border-error rounded-lg hidden">
                <div class="flex items-center gap-2 text-error">
                    <iconify-icon icon="solar:calendar-remove-bold-duotone" class="text-xl"></iconify-icon>
                    <div>
                        <h4 class="font-bold">NATIONAL HOLIDAY</h4>
                        <p id="holiday-name" class="text-sm">This is a Moroccan holiday. No tasks can be created on this day.</p>
                    </div>
                </div>
            </div>
            
            <!-- Tasks Tab -->
            <div id="modal-tasks" class="tab-content">
                <div class="flex justify-between items-center mb-5">
                    <h4 class="text-lg font-medium">Tasks for this date</h4>
                        @if(auth()->user()->hasAnyRole(['supervisor', 'director']))
                            <a href="{{ url('/tasks') }}?new_task=true" id="new-task-modal-btn" class="btn btn-sm btn-primary" data-date="">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="solar:add-circle-bold-duotone" class="iconify w-5 h-5 mr-2 iconify--solar"><path fill="currentColor" d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2s10 4.477 10 10" opacity=".5"></path><path fill="currentColor" d="M12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25z"></path></svg>
                                New Task
                            </a>
                        @endif
                </div>
                
                <div id="tasks-list" class="space-y-4">
                    <!-- Tasks will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Include Livewire Modal Manager -->
<livewire:modals.modal-manager />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make tasks accessible in global scope for debugging
    window.tasks = @json($tasks);
    const projects = @json($projects);
    const assignedTaskIds = @json($assignedTaskIds);
    
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let currentDay = currentDate.getDate();
    let currentView = 'month'; // month, week, day
    let showHolidays = true; // Default to showing holidays
    
    // Moroccan holidays
    const moroccanHolidays = {
        // Fixed holidays (month-day format)
        fixed: {
            '01-01': 'New Year\'s Day',
            '01-11': 'Proclamation of Independence',
            '05-01': 'Labor Day',
            '07-30': 'Throne Day',
            '08-14': 'Oued Ed-Dahab Day',
            '08-20': 'Revolution Day',
            '08-21': 'Youth Day',
            '11-06': 'Green March Day',
            '11-18': 'Independence Day'
        },
        // Islamic holidays (specific dates for 2023-2024)
        islamic: {
            // 2023 Islamic holidays
            '2023-07-19': 'Islamic New Year',
            '2023-07-28': 'Ashura',
            '2023-09-27': 'Prophet\'s Birthday (Mawlid)',
            '2023-04-22': 'Eid al-Fitr (1st day)',
            '2023-04-23': 'Eid al-Fitr (2nd day)',
            '2023-06-29': 'Eid al-Adha (1st day)',
            '2023-06-30': 'Eid al-Adha (2nd day)',
            
            // 2024 Islamic holidays
            '2024-07-08': 'Islamic New Year',
            '2024-07-17': 'Ashura',
            '2024-09-16': 'Prophet\'s Birthday (Mawlid)',
            '2024-04-10': 'Eid al-Fitr (1st day)',
            '2024-04-11': 'Eid al-Fitr (2nd day)',
            '2024-06-17': 'Eid al-Adha (1st day)',
            '2024-06-18': 'Eid al-Adha (2nd day)'
        }
    };
    
    // Check if a date is a Moroccan holiday
    function isHoliday(date) {
        if (!showHolidays) return false;
        
        // Format for fixed date check (MM-DD)
        const monthDay = `${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        
        // Check fixed holidays
        if (moroccanHolidays.fixed[monthDay]) {
            return moroccanHolidays.fixed[monthDay];
        }
        
        // Format for Islamic date check (YYYY-MM-DD)
        const fullDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        
        // Check Islamic holidays
        if (moroccanHolidays.islamic[fullDate]) {
            return moroccanHolidays.islamic[fullDate];
        }
        
        return false;
    }
    
    // Listen for Livewire events to refresh calendar
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('taskCreated', () => {
            // Reload tasks via AJAX instead of refreshing the whole page
            fetchUpdatedTasks();
        });
        
        Livewire.on('taskUpdated', () => {
            // Reload tasks via AJAX instead of refreshing the whole page
            fetchUpdatedTasks();
        });
        
        Livewire.on('taskDeleted', () => {
            // Reload tasks via AJAX instead of refreshing the whole page
            fetchUpdatedTasks();
        });
    });
    
    // Store task colors to keep them consistent
    const taskColors = {};
    
    // Function to generate colors based on task priority
    function getTaskColor(task) {
        // If we've already calculated this task's color, return it
        if (taskColors[task.id]) {
            return taskColors[task.id];
        }
        
        // Get priority-based colors
        let colorLight, colorDark;
        
        switch((task.priority || '').toLowerCase()) {
            case 'high':
                colorLight = 'hsla(0, 85%, 85%, 0.9)'; // Light red
                colorDark = 'hsla(0, 80%, 40%, 0.9)';  // Dark red
                break;
            case 'medium':
                colorLight = 'hsla(40, 85%, 85%, 0.9)'; // Light amber
                colorDark = 'hsla(40, 80%, 40%, 0.9)';  // Dark amber
                break;
            case 'low':
                colorLight = 'hsla(200, 85%, 85%, 0.9)'; // Light blue
                colorDark = 'hsla(200, 80%, 40%, 0.9)';  // Dark blue
                break;
            default:
                colorLight = 'hsla(260, 60%, 85%, 0.9)'; // Light purple (default)
                colorDark = 'hsla(260, 60%, 40%, 0.9)';  // Dark purple (default)
        }
        
        // Store the colors
        taskColors[task.id] = { light: colorLight, dark: colorDark };
        
        // Determine if we're in dark mode
        const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
        
        // Return appropriate color based on current theme
        return isDarkMode ? colorDark : colorLight;
    }
    
    // Function to update task colors when theme changes
    function updateTaskColors() {
        const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
        
        // Update all task items with their theme-appropriate colors
        document.querySelectorAll('.task-item').forEach(taskItem => {
            const taskId = taskItem.getAttribute('data-task-id');
            if (taskId && taskColors[taskId]) {
                taskItem.style.backgroundColor = isDarkMode ? taskColors[taskId].dark : taskColors[taskId].light;
            }
        });
    }
    
    // Watch for theme changes (using MutationObserver)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'data-theme') {
                updateTaskColors();
            }
        });
    });
    
    // Start observing theme changes
    observer.observe(document.documentElement, { attributes: true });
    
    // Filter states
    let filters = {
        project: '',
        priority: '',
        status: '',
        myTasksOnly: true,
        search: ''
    };
    
    // DOM Elements
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    const todayBtn = document.getElementById('today-btn');
    const currentMonthElement = document.getElementById('current-month');
    const showHolidaysCheckbox = document.getElementById('show-holidays');
    
    // Holiday toggle handler
    showHolidaysCheckbox.addEventListener('change', function() {
        showHolidays = this.checked;
        renderCalendar();
    });
    
    // View Buttons
    const viewMonthBtn = document.getElementById('view-month');
    const viewWeekBtn = document.getElementById('view-week');
    const viewDayBtn = document.getElementById('view-day');
    
    // Filter Elements
    const projectFilter = document.getElementById('project-filter');
    const priorityFilter = document.getElementById('priority-filter');
    const statusFilter = document.getElementById('status-filter');
    const taskSearch = document.getElementById('task-search');
    
    // Modal Tabs
    const tabAllTasks = document.getElementById('tab-all-tasks');
    
    // Populate project options in filters and form
    populateProjectOptions();
    
    // Initialize calendar
    renderCalendar();
    
    // Event listeners for navigation
    prevMonthBtn.addEventListener('click', () => {
        if (currentView === 'month') {
            // Move back one month in month view
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
            }
        } else if (currentView === 'week') {
            // Move back one week in week view
            const currentDate = new Date(currentYear, currentMonth, currentDay);
            currentDate.setDate(currentDate.getDate() - 7);
            currentDay = currentDate.getDate();
            currentMonth = currentDate.getMonth();
            currentYear = currentDate.getFullYear();
        } else if (currentView === 'day') {
            // Move back one day in day view
            const currentDate = new Date(currentYear, currentMonth, currentDay);
            currentDate.setDate(currentDate.getDate() - 1);
            currentDay = currentDate.getDate();
            currentMonth = currentDate.getMonth();
            currentYear = currentDate.getFullYear();
        }
        renderCalendar();
        setTimeout(applyTaskColors, 10);
    });
    
    nextMonthBtn.addEventListener('click', () => {
        if (currentView === 'month') {
            // Move forward one month in month view
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
            }
        } else if (currentView === 'week') {
            // Move forward one week in week view
            const currentDate = new Date(currentYear, currentMonth, currentDay);
            currentDate.setDate(currentDate.getDate() + 7);
            currentDay = currentDate.getDate();
            currentMonth = currentDate.getMonth();
            currentYear = currentDate.getFullYear();
        } else if (currentView === 'day') {
            // Move forward one day in day view
            const currentDate = new Date(currentYear, currentMonth, currentDay);
            currentDate.setDate(currentDate.getDate() + 1);
            currentDay = currentDate.getDate();
            currentMonth = currentDate.getMonth();
            currentYear = currentDate.getFullYear();
        }
        renderCalendar();
        setTimeout(applyTaskColors, 10);
    });
    
    todayBtn.addEventListener('click', () => {
        // Set date to today
        const today = new Date();
        currentDay = today.getDate();
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
        
        // Always show today's view based on current view mode
        if (currentView === 'month') {
            // In month view, just show the current month containing today
        renderCalendar();
        } else if (currentView === 'week' || currentView === 'day') {
            // For week/day view, explicitly go to today
            renderCalendar();
            
            // Scroll to today if needed (for better user experience)
            setTimeout(() => {
                const todayElement = document.querySelector('.is-today');
                if (todayElement) {
                    todayElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        }
    });
    
    // View switch event listeners
    viewMonthBtn.addEventListener('click', () => {
        setActiveView('month');
    });
    
    viewWeekBtn.addEventListener('click', () => {
        setActiveView('week');
    });
    
    viewDayBtn.addEventListener('click', () => {
        setActiveView('day');
    });
    
    // Filter event listeners
    projectFilter.addEventListener('change', () => {
        filters.project = projectFilter.value;
        renderCalendar();
    });
    
    priorityFilter.addEventListener('change', () => {
        filters.priority = priorityFilter.value;
        renderCalendar();
    });
    
    statusFilter.addEventListener('change', () => {
        filters.status = statusFilter.value;
        renderCalendar();
    });
    
    taskSearch.addEventListener('input', () => {
        filters.search = taskSearch.value.toLowerCase();
        renderTasksList(); // Only update the task list, not the whole calendar
    });
    
    // Tab switching in modal
    tabAllTasks.addEventListener('click', () => {
        tabAllTasks.classList.add('tab-active');
    });
    
    // Helper Functions
    function populateProjectOptions() {
        // Add project options to filter dropdown
        projects.forEach(project => {
            const option = document.createElement('option');
            option.value = project.id;
            option.textContent = project.name;
            projectFilter.appendChild(option);
        });
    }
    
    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    // Render calendar function
    function renderCalendar() {
        // Update month/year display
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        
        // Clear previous calendar
        const calendarDaysElement = document.getElementById('calendar-days');
        calendarDaysElement.innerHTML = '';
        
        // Debug task statuses
        if (window.tasks.length > 0) {
            // Remove debug logs
        }
        
        // Use all tasks without filtering by user_id
        const filteredTasks = window.tasks.filter(task => {
            // Apply project filter
            if (filters.project && task.project_id != filters.project) {
                return false;
            }
            
            // Apply priority filter
            if (filters.priority && task.priority && task.priority.toLowerCase() !== filters.priority.toLowerCase()) {
                return false;
            }
            
            // Apply status filter - fix by standardizing comparison
            if (filters.status && task.status) {
                const normalizedTaskStatus = normalizeStatus(task.status);
                const normalizedFilterStatus = normalizeStatus(filters.status);
                
                if (normalizedTaskStatus !== normalizedFilterStatus) {
                    return false;
                }
            }
            
            // Apply search filter
            if (filters.search && !task.title.toLowerCase().includes(filters.search.toLowerCase())) {
                return false;
            }
            
            return true;
        });
        
        // Reset grid classes
        calendarDaysElement.className = 'grid gap-px bg-base-300';
        
        // Render the appropriate view based on the currentView setting
        if (currentView === 'month') {
            calendarDaysElement.classList.add('grid-cols-7');
            renderMonthView(calendarDaysElement, filteredTasks);
        } else if (currentView === 'week') {
            calendarDaysElement.classList.add('grid-cols-1');
            renderWeekView(calendarDaysElement, filteredTasks);
        } else if (currentView === 'day') {
            calendarDaysElement.classList.add('grid-cols-1');
            renderDayView(calendarDaysElement, filteredTasks);
        }
        
        // Apply colors to tasks after rendering
        setTimeout(applyTaskColors, 10);
    }
    
    // Render month view
    function renderMonthView(calendarDaysElement, filteredTasks) {
        let date = new Date(currentYear, currentMonth, 1);
        
        // Start from the first day of the week that contains the first day of the month
        date.setDate(1 - date.getDay());
        
        // Create 6 weeks of calendar
        for (let week = 0; week < 6; week++) {
            for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
                const isToday = date.getDate() === currentDay && date.getMonth() === currentDate.getMonth() && date.getFullYear() === currentDate.getFullYear();
                const isCurrentMonth = date.getMonth() === currentMonth;
                
                // Check if this day is a holiday
                const holidayName = isHoliday(date);
                const isHolidayDay = holidayName !== false;
                
                // Format date as string for data attribute
                const dateStr = date.toISOString().split('T')[0];
                
                // Create day element
                const dayElement = document.createElement('div');
                dayElement.className = `day p-2 bg-base-100 ${isCurrentMonth ? '' : 'opacity-40'} ${isToday ? 'ring-2 ring-primary ring-inset' : ''}`;
                if (isHolidayDay) {
                    dayElement.classList.add('bg-error/10');
                    dayElement.classList.add('holiday-cell');
                }
                dayElement.dataset.date = dateStr;
                
                // Create date number element
                const dateElement = document.createElement('div');
                dateElement.className = 'flex justify-between items-center mb-2';
                
                // Date number
                const dateNumber = document.createElement('span');
                dateNumber.className = `text-sm font-medium ${isToday ? 'bg-primary text-primary-content rounded-full w-6 h-6 flex items-center justify-center' : ''}`;
                dateNumber.textContent = date.getDate();
                dateElement.appendChild(dateNumber);
                
                // Add button (only for non-holidays, current/future dates, and authorized users)
                // if (!isHolidayDay && isCurrentMonth && @json(auth()->user()->hasRole(['director', 'supervisor']))) {
                //     const addButton = document.createElement('button');
                //     addButton.className = 'btn btn-xs btn-ghost btn-circle';
                //     addButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="solar:add-circle-bold-duotone" class="iconify w-5 h-5 mr-2 iconify--solar"><path fill="currentColor" d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2s10 4.477 10 10" opacity=".5"></path><path fill="currentColor" d="M12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25z"></path></svg>';
                //     addButton.addEventListener('click', (e) => {
                //         e.stopPropagation();
                //         openTaskCreationPage(dateStr);
                //     });
                //     dateElement.appendChild(addButton);
                // }
                
                dayElement.appendChild(dateElement);
                
                // Add holiday indicator if it's a holiday
                if (isHolidayDay) {
                    const holidayIndicator = document.createElement('div');
                    holidayIndicator.className = 'mb-1 p-2 rounded border-l-4 border-error bg-gradient-to-r from-error/20 to-error/5 shadow-sm';
                    
                    const holidayHeader = document.createElement('div');
                    holidayHeader.className = 'text-xs font-bold text-error flex items-center gap-1 mb-1';
                    holidayHeader.innerHTML = '<iconify-icon icon="solar:calendar-remove-bold-duotone" class="text-xl"></iconify-icon><span>NATIONAL HOLIDAY</span>';
                    
                    const holidayNameElement = document.createElement('div');
                    holidayNameElement.className = 'text-sm text-error/90 font-medium truncate';
                    holidayNameElement.textContent = holidayName;
                    
                    holidayIndicator.appendChild(holidayHeader);
                    holidayIndicator.appendChild(holidayNameElement);
                    dayElement.appendChild(holidayIndicator);
                }
                
                // Add tasks for this day (using filtered tasks instead of window.tasks)
                const dayTasks = filterTasksForDate(filteredTasks, date);
                
                dayTasks.forEach(task => {
                    const taskElement = createTaskElement(task, isHolidayDay);
                    dayElement.appendChild(taskElement);
                });
                
                // Add click handler to open the modal with debugging output
                dayElement.addEventListener('click', function(e) {
                    // Ignore clicks on task items (they have their own handlers)
                    if (e.target.closest('.task-item')) {
                        return;
                    }
                    // Store the tasks specifically for this day in a data attribute
                    // to ensure we're using the correct tasks when the modal opens
                    const dayTasksJSON = JSON.stringify(dayTasks);
                    dayElement.dataset.tasks = dayTasksJSON;
                    
                    // Open modal with the exact tasks for this specific day
                    openDayTasksModal(new Date(dateStr + 'T00:00:00'), holidayName, dayTasks);
                });
                
                calendarDaysElement.appendChild(dayElement);
                
                // Move to next day
                date = new Date(date.getTime() + 24 * 60 * 60 * 1000);
            }
        }
    }
    
    // Render week view
    function renderWeekView(calendarDaysElement, filteredTasks) {
        // Start from the beginning of the week containing the current day
        let date = new Date(currentYear, currentMonth, currentDay);
        date.setDate(date.getDate() - date.getDay()); // Go to Sunday
        
        // Create a week container
        const weekContainer = document.createElement('div');
        weekContainer.className = 'grid grid-cols-7 gap-px bg-base-300 w-full';
        
        // Create 7 days for the week
        for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
            const isToday = date.getDate() === currentDate.getDate() && 
                          date.getMonth() === currentDate.getMonth() && 
                          date.getFullYear() === currentDate.getFullYear();
            
            // Check if this day is a holiday
            const holidayName = isHoliday(date);
            const isHolidayDay = holidayName !== false;
            
            // Format date as string for data attribute
            const dateStr = date.toISOString().split('T')[0];
            
            // Create day element
            const dayElement = document.createElement('div');
            dayElement.className = `week-day p-3 bg-base-100 min-h-[300px] ${isToday ? 'is-today' : ''}`;
            if (isHolidayDay) {
                dayElement.classList.add('bg-error/10');
                dayElement.classList.add('holiday-cell');
            }
            dayElement.dataset.date = dateStr;
            
            // Create day header
            const dayHeader = document.createElement('div');
            dayHeader.className = 'week-header mb-3 pb-2 border-b';
            
            const dayName = document.createElement('div');
            dayName.className = 'text-center font-bold';
            dayName.textContent = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][dayOfWeek];
            
            const dayDate = document.createElement('div');
            dayDate.className = `text-center font-medium ${isToday ? 'text-primary' : ''}`;
            dayDate.textContent = date.getDate();
            
            dayHeader.appendChild(dayName);
            dayHeader.appendChild(dayDate);
            dayElement.appendChild(dayHeader);
            
            // Holiday indicator
            if (isHolidayDay) {
                const holidayIndicator = document.createElement('div');
                holidayIndicator.className = 'mb-3 p-2 rounded border-l-4 border-error bg-gradient-to-r from-error/20 to-error/5 shadow-sm';
                
                const holidayNameElement = document.createElement('div');
                holidayNameElement.className = 'text-sm text-error/90 font-medium text-center';
                holidayNameElement.textContent = holidayName;
                
                holidayIndicator.appendChild(holidayNameElement);
                dayElement.appendChild(holidayIndicator);
            }
            
            // Task container
            const taskContainer = document.createElement('div');
            taskContainer.className = 'space-y-2';
            
            // Add tasks for this day
            const dayTasks = filterTasksForDate(filteredTasks, date);
            if (dayTasks.length === 0) {
                const emptyState = document.createElement('div');
                emptyState.className = 'text-center text-base-content/50 text-sm p-4';
                emptyState.textContent = 'No tasks';
                taskContainer.appendChild(emptyState);
            } else {
                dayTasks.forEach(task => {
                    const taskElement = createTaskElement(task, isHolidayDay);
                    taskElement.classList.add('p-2'); // Make tasks bigger in week view
                    taskContainer.appendChild(taskElement);
                });
            }
            
            dayElement.appendChild(taskContainer);
            
            // Add click handler for empty space
            dayElement.addEventListener('click', function(e) {
                if (e.target.closest('.task-item')) {
                    return;
                }
                
                openDayTasksModal(new Date(dateStr + 'T00:00:00'), holidayName, dayTasks);
            });
            
            weekContainer.appendChild(dayElement);
            
            // Move to next day
            date = new Date(date.getTime() + 24 * 60 * 60 * 1000);
        }
        
        calendarDaysElement.appendChild(weekContainer);
    }
    
    // Render day view
    function renderDayView(calendarDaysElement, filteredTasks) {
        // Use the current selected day
        const date = new Date(currentYear, currentMonth, currentDay);
        
        // Check if this day is a holiday
        const holidayName = isHoliday(date);
        const isHolidayDay = holidayName !== false;
        
        // Format date as string for data attribute
        const dateStr = date.toISOString().split('T')[0];
        const isToday = date.getDate() === currentDate.getDate() && 
                       date.getMonth() === currentDate.getMonth() && 
                       date.getFullYear() === currentDate.getFullYear();
        
        // Create day view container
        const dayViewContainer = document.createElement('div');
        dayViewContainer.className = `day-view-container bg-base-100 rounded-lg overflow-hidden shadow-md ${isToday ? 'is-today' : ''}`;
        dayViewContainer.dataset.date = dateStr;
        
        // Create day header
        const header = document.createElement('div');
        header.className = `day-view-header p-6 ${isToday ? 'bg-primary/10' : 'bg-base-200'}`;
        
        const dateElement = document.createElement('h2');
        dateElement.className = 'day-view-title mb-2';
        
        // Format the date
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateElement.textContent = date.toLocaleDateString('en-US', options);
        
        header.appendChild(dateElement);
        
        // Add holiday notice if it's a holiday
        if (isHolidayDay) {
            const holidayNotice = document.createElement('div');
            holidayNotice.className = 'mt-2 p-3 bg-error/10 border-l-4 border-error rounded-md';
            
            const holidayContent = document.createElement('div');
            holidayContent.className = 'flex items-center gap-2 text-error';
            holidayContent.innerHTML = `<iconify-icon icon="solar:calendar-remove-bold-duotone" class="text-xl"></iconify-icon>
                                     <div>
                                         <h4 class="font-bold">NATIONAL HOLIDAY</h4>
                                         <p class="text-sm">${holidayName}</p>
                                     </div>`;
            
            holidayNotice.appendChild(holidayContent);
            header.appendChild(holidayNotice);
        }
        
        dayViewContainer.appendChild(header);
        
        // Create day content
        const content = document.createElement('div');
        content.className = 'day-view-content p-6';
        
        // Add today's tasks
        const taskSection = document.createElement('div');
        taskSection.className = 'mb-6';
        
        const taskHeader = document.createElement('div');
        taskHeader.className = 'flex justify-between items-center mb-4';
        
        const taskTitle = document.createElement('h3');
        taskTitle.className = 'text-lg font-bold';
        taskTitle.textContent = 'Tasks';
        
        const addButton = document.createElement('a');
        addButton.href = `{{ url('/tasks') }}?new_task=true&due_date=${dateStr}`;
        addButton.className = 'btn btn-primary btn-sm';
        addButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="solar:add-circle-bold-duotone" class="iconify w-5 h-5 mr-2 iconify--solar"><path fill="currentColor" d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2s10 4.477 10 10" opacity=".5"></path><path fill="currentColor" d="M12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25z"></path></svg> Add Task';
        
        taskHeader.appendChild(taskTitle);
        
        // Only add the button if it's not a holiday
        if (!isHolidayDay) {
            taskHeader.appendChild(addButton);
        }
        
        taskSection.appendChild(taskHeader);
        
        // Task list
        const taskList = document.createElement('div');
        taskList.className = 'space-y-3';
        
        // Get tasks for this day
        const dayTasks = filterTasksForDate(filteredTasks, date);
        
        if (dayTasks.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'flex flex-col items-center justify-center py-10 text-base-content/60';
            
            const iconContainer = document.createElement('div');
            iconContainer.className = 'w-16 h-16 rounded-full bg-base-200 flex items-center justify-center mb-4';
            iconContainer.innerHTML = '<iconify-icon icon="solar:calendar-minimalistic-bold-duotone" style="font-size: 1.75rem"></iconify-icon>';
            
            const emptyMessage = document.createElement('h4');
            emptyMessage.className = 'font-medium text-lg mb-1';
            emptyMessage.textContent = 'No tasks scheduled for today';
            
            emptyState.appendChild(iconContainer);
            emptyState.appendChild(emptyMessage);
            
            if (!isHolidayDay) {
                const helperText = document.createElement('p');
                helperText.className = 'text-sm';
                helperText.textContent = 'Click the "Add Task" button to create a new task.';
                emptyState.appendChild(helperText);
            }
            
            taskList.appendChild(emptyState);
        } else {
            // Create detailed task cards for day view
            dayTasks.forEach(task => {
                const taskCard = document.createElement('div');
                taskCard.className = 'card bg-base-100 shadow-sm border border-base-300 hover:shadow-md transition-shadow';
                
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-4';
                
                const cardTitle = document.createElement('h4');
                cardTitle.className = 'card-title text-base font-bold mb-2';
                cardTitle.textContent = task.title;
                
                cardBody.appendChild(cardTitle);
                
                // Priority badge
                if (task.priority) {
                    const priorityBadge = document.createElement('div');
                    priorityBadge.className = `badge ${getPriorityBadgeClass(task.priority)} text-xs mb-2`;
                    priorityBadge.textContent = task.priority.toUpperCase();
                    cardBody.appendChild(priorityBadge);
                }
                
                // Description if available
                if (task.description) {
                    const description = document.createElement('p');
                    description.className = 'text-sm mb-3 line-clamp-2';
                    description.textContent = task.description;
                    cardBody.appendChild(description);
                }
                
                // Status information
                if (task.status) {
                    const statusInfo = document.createElement('div');
                    statusInfo.className = 'flex items-center justify-between bg-base-200/50 p-3 rounded mb-2';
                    
                    const statusLabel = document.createElement('span');
                    statusLabel.className = 'font-medium';
                    statusLabel.textContent = 'Status';
                    
                    const statusValue = document.createElement('span');
                    statusValue.className = `badge ${getStatusBadgeClass(task.status)}`;
                    statusValue.textContent = formatStatus(task.status);
                    
                    statusInfo.appendChild(statusLabel);
                    statusInfo.appendChild(statusValue);
                    cardBody.appendChild(statusInfo);
                }
                
                // Action buttons
                const actions = document.createElement('div');
                actions.className = 'card-actions justify-end';
                
                const viewButton = document.createElement('a');
                viewButton.href = `{{ url('/tasks') }}?open_task=${task.id}#task-${task.id}`
                viewButton.className = 'btn btn-sm btn-primary';
                viewButton.textContent = 'View';
                
                actions.appendChild(viewButton);
                cardBody.appendChild(actions);
                
                taskCard.appendChild(cardBody);
                taskList.appendChild(taskCard);
                
                // Add click handler
                taskCard.addEventListener('click', (e) => {
                    if (!e.target.closest('a') && !e.target.closest('button')) {
                        window.location.href = `{{ url('/tasks') }}?open_task=${task.id}#task-${task.id}`;

                        
                    }
                });
            });
        }
        
        taskSection.appendChild(taskList);
        content.appendChild(taskSection);
        dayViewContainer.appendChild(content);
        
        calendarDaysElement.appendChild(dayViewContainer);
    }
    
    function openTaskCreationPage(dateStr) {
        window.location.href = `{{ route('tasks.create') }}?from=calendar&due_date=${dateStr}`;
    }
    
    function createTaskElement(task, isHolidayDay) {
        const taskElement = document.createElement('div');
        taskElement.className = `task-item cursor-pointer mb-1 p-1 text-xs rounded ${isHolidayDay ? 'opacity-50' : ''} hover:brightness-90 hover:shadow-md transition-all duration-200 border border-transparent hover:border-base-content/20`;
        taskElement.dataset.taskId = task.id;
        taskElement.dataset.status = task.status || 'unknown'; // Add status for debugging
        
        // Create a flex container to position content and icons
        const taskContainer = document.createElement('div');
        taskContainer.className = 'flex justify-between items-center w-full';
        
        // Create task title element
        const taskContent = document.createElement('div');
        taskContent.className = 'truncate flex-1';
        taskContent.textContent = task.title;
        
        // Add repetitive task indicator if applicable
        const taskIcons = document.createElement('div');
        taskIcons.className = 'flex-shrink-0 flex items-center ml-1';
        
        if (task.is_repetitive || task.repetitive_task) {
            const repeatIcon = document.createElement('span');
            repeatIcon.className = 'ml-1 opacity-70';
            repeatIcon.setAttribute('title', 'Repetitive Task');
            repeatIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M9.53 2.47a.75.75 0 0 0-1.06 1.06l.72.72H9a7.75 7.75 0 1 0 0 15.5h.5a.75.75 0 0 0 0-1.5H9a6.25 6.25 0 0 1 0-12.5h2a.75.75 0 0 0 .53-1.28z" clip-rule="evenodd"/><path fill="currentColor" d="M14.5 4.25a.75.75 0 0 0 0 1.5h.5a6.25 6.25 0 1 1 0 12.5h-2a.75.75 0 0 0-.53 1.28l2 2a.75.75 0 0 0 1.06-1.06l-.72-.72H15a7.75 7.75 0 0 0 0-15.5z" opacity="0.5"/></svg>'; // Repeat emoji
            taskIcons.appendChild(repeatIcon);
        }
        
        // Add elements to container
        taskContainer.appendChild(taskContent);
        taskContainer.appendChild(taskIcons);
        taskElement.appendChild(taskContainer);
        
        // Set background color
        taskElement.style.backgroundColor = getTaskColor(task);
        
        // Add click handler that stops propagation
        taskElement.addEventListener('click', (e) => {
            e.stopPropagation();
            openTaskDetailsModal(task);
        });
        
        return taskElement;
    }
    
    function openTaskDetailsModal(task) {
        
        // Get all required elements
        const modal = document.getElementById('task-modal');
        const modalDate = document.getElementById('modal-date');
        const tasksList = document.getElementById('tasks-list');
        const newTaskBtn = document.getElementById('new-task-modal-btn');
        const holidayNotice = document.getElementById('holiday-notice');
        
        // Check if required elements exist
        if (!modal || !modalDate || !tasksList) {
            console.error('Required modal elements not found:', {
                modal: !!modal,
                modalDate: !!modalDate,
                tasksList: !!tasksList
            });
            return; // Exit the function if required elements are missing
        }
        
        // Format the date for display
        let dateToShow = new Date();
        if (task.due_date) {
            dateToShow = new Date(task.due_date);
        }
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        modalDate.textContent = dateToShow.toLocaleDateString('en-US', options);
        
        // Clear existing tasks
        tasksList.innerHTML = '';
        
        // Only manipulate optional elements if they exist
        if (holidayNotice) {
            holidayNotice.classList.add('hidden');
        }
        
        if (newTaskBtn) {
            // Show new task button
            newTaskBtn.classList.remove('hidden');
            
            // Set the date for the new task button
            const dateStr = dateToShow.toISOString().split('T')[0];
            newTaskBtn.setAttribute('data-date', dateStr);
            newTaskBtn.href = `{{ url('/tasks') }}?new_task=true&due_date=${dateStr}`;
        }
        
        try {
            // Create and add the task card
            const taskCard = createTaskCard(task);
            if (taskCard) {
                tasksList.appendChild(taskCard);
            }
            
            // Open the modal
            modal.showModal();
        } catch (error) {
            console.error('Error creating task card:', error);
        }
    }
    
    function createTaskCard(task) {
        // Create task card for the modal view
        const taskCard = document.createElement('div');
        taskCard.className = 'card bg-base-100 shadow-md rounded-lg overflow-hidden cursor-pointer hover:shadow-lg transition-all border border-transparent hover:border-primary/20';
        taskCard.dataset.taskId = task.id;
        
        // Make the entire card clickable to navigate to task details
        taskCard.addEventListener('click', (e) => {
            // Only trigger if not clicking on a button element to avoid conflict with edit/close buttons
            if (!e.target.closest('button') && !e.target.closest('a')) {
                // Close the current modal
                document.getElementById('task-modal').close();
                // Use both query parameter and hash to maximize the chance of success
                window.location.href = `{{ url('/tasks') }}?open_task=${task.id}#task-${task.id}`;
            }
        });

        // Get priority class based on the task priority
        const priorityClass = getPriorityClass(task.priority);
        
        // Create a clean modern header
        const header = document.createElement('div');
        header.className = 'p-4 border-l-4 ' + borderClassFromPriority(task.priority);
        
        // Create title container to add title and repetitive indicator
        const titleContainer = document.createElement('div');
        titleContainer.className = 'flex items-center justify-between';
        
        // Clear, prominent task title with visual indication it's clickable
        const title = document.createElement('h3');
        title.className = 'text-xl font-bold text-base-content hover:text-primary transition-colors';
        title.textContent = task.title;
        
        // Add repetitive indicator if needed
        if (task.is_repetitive || task.repetitive_task) {
            const repeatBadge = document.createElement('div');
            repeatBadge.className = 'badge badge-accent ml-2 flex items-center gap-1';
            repeatBadge.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M9.53 2.47a.75.75 0 0 0-1.06 1.06l.72.72H9a7.75 7.75 0 1 0 0 15.5h.5a.75.75 0 0 0 0-1.5H9a6.25 6.25 0 0 1 0-12.5h2a.75.75 0 0 0 .53-1.28z" clip-rule="evenodd"></path><path fill="currentColor" d="M14.5 4.25a.75.75 0 0 0 0 1.5h.5a6.25 6.25 0 1 1 0 12.5h-2a.75.75 0 0 0-.53 1.28l2 2a.75.75 0 0 0 1.06-1.06l-.72-.72H15a7.75 7.75 0 0 0 0-15.5z" opacity="0.5"></path></svg> Repeats';
            repeatBadge.title = 'This is a repetitive task';
            titleContainer.appendChild(title);
            titleContainer.appendChild(repeatBadge);
        } else {
            titleContainer.appendChild(title);
        }
        
        header.appendChild(titleContainer);
        
        // Priority indicator as a simple badge
        if (task.priority) {
            const priorityContainer = document.createElement('div');
            priorityContainer.className = 'flex items-center gap-2 mt-2';
            
            const priorityLabel = document.createElement('span');
            priorityLabel.className = 'text-xs text-base-content/60';
            priorityLabel.textContent = 'Priority:';
            
            const priorityBadge = document.createElement('div');
            priorityBadge.className = `badge ${priorityClass} text-${priorityClass}-content font-medium`;
            priorityBadge.textContent = task.priority.toUpperCase();
            
            priorityContainer.appendChild(priorityLabel);
            priorityContainer.appendChild(priorityBadge);
            header.appendChild(priorityContainer);
        }
        
        taskCard.appendChild(header);
        
        // Card body - clean and minimal
        const cardBody = document.createElement('div');
        cardBody.className = 'p-4 space-y-4 border-t border-base-200';
        
        // Status information
        if (task.status) {
            const statusInfo = document.createElement('div');
            statusInfo.className = 'flex items-center justify-between bg-base-200/50 p-3 rounded mb-2';
            
            const statusLabel = document.createElement('span');
            statusLabel.className = 'font-medium';
            statusLabel.textContent = 'Status';
            
            const statusValue = document.createElement('span');
            statusValue.className = `badge ${getStatusBadgeClass(task.status)}`;
            statusValue.textContent = formatStatus(task.status);
            
            statusInfo.appendChild(statusLabel);
            statusInfo.appendChild(statusValue);
            cardBody.appendChild(statusInfo);
        }
        
        // Due date with clear labeling
        if (task.due_date) {
            const dueInfo = document.createElement('div');
            dueInfo.className = 'flex items-center justify-between bg-base-200/50 p-3 rounded';
            
            const dueLabel = document.createElement('span');
            dueLabel.className = 'font-medium';
            dueLabel.textContent = 'Due Date';
            
            const dueDate = document.createElement('span');
            dueDate.className = 'text-base-content font-bold';
            const date = new Date(task.due_date);
            dueDate.textContent = date.toLocaleDateString(undefined, {year: 'numeric', month: 'short', day: 'numeric'});
            
            dueInfo.appendChild(dueLabel);
            dueInfo.appendChild(dueDate);
            cardBody.appendChild(dueInfo);
        }
        
        // Project name with clear labeling
        if (task.project) {
            const projectInfo = document.createElement('div');
            projectInfo.className = 'flex items-center justify-between p-3';
            
            const projectLabel = document.createElement('span');
            projectLabel.className = 'font-medium';
            projectLabel.textContent = 'Project';
            
            const projectName = document.createElement('span');
            projectName.className = 'text-base-content';
            projectName.textContent = task.project.name;
            
            projectInfo.appendChild(projectLabel);
            projectInfo.appendChild(projectName);
            cardBody.appendChild(projectInfo);
        }
        
        // Actions
        const actions = document.createElement('div');
        actions.className = 'flex justify-end gap-2 mt-4 pt-3 border-t border-base-200';
        
        // Close button
        const closeButton = document.createElement('button');
        closeButton.className = 'btn btn-sm';
        closeButton.textContent = 'Close';
        closeButton.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent triggering the card click
            document.getElementById('task-modal').close();
        });
        
        // Edit button - only show for authorized users
        @if(auth()->user()->hasRole(['director', 'supervisor']))
        const editButton = document.createElement('button');
        editButton.className = 'btn btn-sm btn-primary';
        editButton.textContent = 'Edit';
        editButton.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent triggering the card click
            document.getElementById('task-modal').close();
            // Use the same pattern as task viewing but with edit_task parameter
            window.location.href = `{{ url('/tasks') }}?edit_task=${task.id}#task-${task.id}`;
        });
        actions.appendChild(editButton);
        @endif
        
        actions.appendChild(closeButton);
        cardBody.appendChild(actions);
        
        taskCard.appendChild(cardBody);
        
        return taskCard;
    }
    
    function borderClassFromPriority(priority) {
        switch((priority || '').toLowerCase()) {
            case 'high':
                return 'border-error';
            case 'medium':
                return 'border-warning';
            case 'low':
                return 'border-info';
            default:
                return 'border-primary';
        }
    }
    
    function getPriorityClass(priority) {
        // Return DaisyUI classes for different priorities
        switch((priority || '').toLowerCase()) {
            case 'high':
                return 'bg-error';
            case 'medium':
                return 'bg-warning';
            case 'low':
                return 'bg-info';
            default:
                return 'bg-primary';
        }
    }
    
    function formatStatus(status) {
        return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
    
    function normalizeStatus(status) {
        if (!status) return '';
        
        // Convert to lowercase and trim
        let normalized = status.toLowerCase().trim();
        
        // Replace spaces with underscores
        normalized = normalized.replace(/\s+/g, '_');
        
        // Handle common variations
        switch(normalized) {
            // Current status values
            case 'to_do':
            case 'todo':
            case 'not_started':
                return 'todo';
                
            case 'in_progress':
            case 'inprogress':
            case 'in-progress':
                return 'in_progress';
                
            // Map completed to pending_approval since they represent the same state in your workflow
            case 'complete':
            case 'completed':
            case 'done':
            case 'pending_approval':
            case 'needs_approval':
            case 'need_approval':
            case 'need_to_approve':
            case 'needs_to_be_approved':
                return 'pending_approval';
                
            case 'approved':
                return 'approved';
                
            default:
                return normalized;
        }
    }
    
    function getStatusBadgeClass(status) {
        if (!status) return 'badge-ghost';
        
        const normalizedStatus = normalizeStatus(status);
        
        switch(normalizedStatus) {
            case 'in_progress':
                return 'badge-warning';
            case 'todo':
                return 'badge-info';
            case 'pending_approval':
                return 'badge-secondary';
            case 'approved':
                return 'badge-accent';
            default:
                return 'badge-ghost';
        }
    }
    
    function getPriorityBadgeClass(priority) {
        switch(priority.toLowerCase()) {
            case 'high':
                return 'badge-error';
            case 'medium':
                return 'badge-warning';
            case 'low':
                return 'badge-info';
            default:
                return 'badge-ghost';
        }
    }
    
    function getPriorityBorderColor(priority) {
        switch((priority || '').toLowerCase()) {
                case 'high':
                return 'hsl(var(--er))';
                case 'medium':
                return 'hsl(var(--wa))';
                case 'low':
                return 'hsl(var(--in))';
                default:
                return 'hsl(var(--p))';
        }
    }
    
    function filterTasksForDate(tasks, date) {
        // Ensure tasks is an array before filtering
        if (!Array.isArray(tasks)) {
            return [];
        }
        
        const filteredTasks = tasks.filter(task => {
            if (!task.due_date) return false;
            
            const dueDate = new Date(task.due_date);
            return dueDate.getFullYear() === date.getFullYear() &&
                   dueDate.getMonth() === date.getMonth() &&
                   dueDate.getDate() === date.getDate();
        });
        
        return filteredTasks;
    }
    
    function openDayTasksModal(date, holidayName, dayTasks) {
        const modal = document.getElementById('task-modal');
        const modalDate = document.getElementById('modal-date');
        const tasksList = document.getElementById('tasks-list');
        const newTaskBtn = document.getElementById('new-task-modal-btn');
        const holidayNotice = document.getElementById('holiday-notice');
        const holidayNameElement = document.getElementById('holiday-name');
        
        // Check if all required elements exist
        if (!modal || !modalDate || !tasksList) {
            return;
        }
        
        // Format the date for display
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        modalDate.textContent = date.toLocaleDateString('en-US', options);
        
        // Clear existing tasks
        tasksList.innerHTML = '';
        
        // Show or hide holiday notice if the elements exist
        if (holidayNotice && holidayNameElement) {
            if (holidayName) {
                holidayNotice.classList.remove('hidden');
                holidayNameElement.textContent = holidayName;
                if (newTaskBtn) {
                    newTaskBtn.classList.add('hidden'); // Hide the new task button on holidays
                }
            } else {
                holidayNotice.classList.add('hidden');
                if (newTaskBtn) {
                    newTaskBtn.classList.remove('hidden');
                }
            }
        }
        
        // Set the date for the new task button if it exists
        if (newTaskBtn) {
            const dateStr = date.toISOString().split('T')[0];
            newTaskBtn.setAttribute('data-date', dateStr);
            newTaskBtn.href = `{{ url('/tasks') }}?new_task=true&due_date=${dateStr}`;
        }
        
        // Add tasks to the list
        if (!dayTasks || dayTasks.length === 0) {
            // Create an empty state that matches the theme
            const emptyState = document.createElement('div');
            emptyState.className = 'flex flex-col items-center justify-center py-8 text-base-content/70';
            
            // Icon container with theme-aware styling
            const iconContainer = document.createElement('div');
            iconContainer.className = 'w-16 h-16 rounded-full bg-base-200 flex items-center justify-center mb-4';
            iconContainer.innerHTML = '<iconify-icon icon="solar:calendar-minimalistic-bold-duotone" style="font-size: 1.75rem"></iconify-icon>';
            
            // Empty message
            const emptyText = document.createElement('h4');
            emptyText.className = 'font-medium text-lg mb-2';
            emptyText.textContent = 'No tasks for this day';
            
            // Helper text
            const helperText = document.createElement('p');
            helperText.className = 'text-sm text-center max-w-xs';
            helperText.textContent = 'Click the "New Task" button to create a task for this date.';
            
            emptyState.appendChild(iconContainer);
            emptyState.appendChild(emptyText);
            if (!holidayName) {
                emptyState.appendChild(helperText);
            }
            
            tasksList.appendChild(emptyState);
        } else {
            // First add a helpful header if multiple tasks
            if (dayTasks.length > 1) {
                const taskCountHeader = document.createElement('h4');
                taskCountHeader.className = 'text-sm font-medium text-base-content/60 mb-3';
                taskCountHeader.textContent = `${dayTasks.length} tasks scheduled for this day`;
                tasksList.appendChild(taskCountHeader);
            }
            
            // Then add each task card
            dayTasks.forEach(task => {
                const taskCard = createTaskCard(task);
                tasksList.appendChild(taskCard);
            });
        }
        
        // Open the modal
        modal.showModal();
    }
    
    function setActiveView(view) {
        currentView = view;
        
        // Update button states
        viewMonthBtn.classList.remove('btn-active');
        viewWeekBtn.classList.remove('btn-active');
        viewDayBtn.classList.remove('btn-active');
        
        // Update navigation button tooltips based on view
        if (view === 'month') {
            prevMonthBtn.setAttribute('title', 'Previous Month');
            nextMonthBtn.setAttribute('title', 'Next Month');
                viewMonthBtn.classList.add('btn-active');
        } else if (view === 'week') {
            prevMonthBtn.setAttribute('title', 'Previous Week');
            nextMonthBtn.setAttribute('title', 'Next Week');
                viewWeekBtn.classList.add('btn-active');
        } else if (view === 'day') {
            prevMonthBtn.setAttribute('title', 'Previous Day');
            nextMonthBtn.setAttribute('title', 'Next Day');
                viewDayBtn.classList.add('btn-active');
        }
        
        renderCalendar();
    }
    
    function applyTaskColors() {
        // Find all elements with task-id attributes
        document.querySelectorAll('[data-task-id]').forEach(taskItem => {
            const taskId = taskItem.getAttribute('data-task-id');
            if (taskId) {
                // Find matching task
                const task = window.tasks.find(t => t.id == taskId);
                if (task) {
                    // Get priority-based color
                    let color;
                    const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
                    
                    switch((task.priority || '').toLowerCase()) {
                        case 'high':
                            color = isDarkMode ? 'hsla(0, 80%, 40%, 0.9)' : 'hsla(0, 85%, 85%, 0.9)'; // Red
                            break;
                        case 'medium':
                            color = isDarkMode ? 'hsla(40, 80%, 40%, 0.9)' : 'hsla(40, 85%, 85%, 0.9)'; // Amber
                            break;
                        case 'low':
                            color = isDarkMode ? 'hsla(200, 80%, 40%, 0.9)' : 'hsla(200, 85%, 85%, 0.9)'; // Blue
                            break;
                        default:
                            color = isDarkMode ? 'hsla(260, 60%, 40%, 0.9)' : 'hsla(260, 60%, 85%, 0.9)'; // Purple
                    }
                    
                    // Apply color to the task element
                    taskItem.style.backgroundColor = color;
                }
            }
        });
    }
    
    // Apply colors on initial load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(applyTaskColors, 100);
    });
    
    // Updated function to fetch tasks
    function fetchUpdatedTasks() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                try {
                    const scriptContent = Array.from(doc.querySelectorAll('script'))
                        .find(script => script.textContent.includes('const tasks ='))?.textContent;
                    
                    if (scriptContent) {
                        const tasksMatch = scriptContent.match(/const tasks = (.*?);/);
                        if (tasksMatch && tasksMatch[1]) {
                            window.tasks = JSON.parse(tasksMatch[1]);
                            renderCalendar();
                        }
                    }
                } catch (error) {
                    window.location.reload();
                }
            })
            .catch(() => {
                window.location.reload();
            });
    }
});
</script>

<style>
.calendar-container {
    width: 100%;
    min-width: 800px;
}

.day {
    min-height: 120px;
    min-width: 100px;
    position: relative;
    padding: 0;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.day:hover {
    border-color: hsl(var(--p) / 0.3);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    font-weight: 500;
    border-bottom: 1px solid rgba(var(--b3) / 0.1);
    background-color: rgba(var(--b2) / 0.3);
}

.day-number {
    height: 28px;
    width: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 600;
    transition: all 0.2s ease;
}

.is-today .day-number {
    background-color: hsl(var(--p));
    color: hsl(var(--pc));
}

.day-content {
    padding: 0.5rem;
    overflow-y: auto;
    max-height: calc(100% - 36px);
}

.task-item {
    width: 90%; /* Decrease width to 90% of the day cell */
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 0.3rem;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    padding: 0.25rem 0.5rem;
    cursor: pointer;
    border-radius: 4px;
    font-size: 0.75rem;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    color: hsl(var(--bc));
}

.task-item:hover {
    transform: translateX(2px);
}

/* Add a subtle pulsing animation for task items to draw attention */
@keyframes pulse-border {
    0% { border-color: transparent; }
    50% { border-color: hsl(var(--p) / 0.5); }
    100% { border-color: transparent; }
}

.task-item:active {
    transform: translateY(0);
    animation: pulse-border 0.3s ease;
}

.task-item.priority-high {
    border-left-color: hsl(var(--er));
}

.task-item.priority-medium {
    border-left-color: hsl(var(--wa));
}

.task-item.priority-low {
    border-left-color: hsl(var(--in));
}

.current-month {
    background-color: hsl(var(--b1));
}

.prev-month, .next-month {
    background-color: hsl(var(--b2) / 0.5);
    opacity: 0.7;
}

.tab-content {
    display: block;
}

.tab-content.hidden {
    display: none;
}

.project-tag {
    display: inline-block;
    padding: 0.15rem 0.3rem;
    border-radius: 3px;
    font-size: 0.7rem;
    color: hsl(var(--pc));
    background-color: hsl(var(--p) / 0.7);
    white-space: nowrap;
}

/* Week View Styles */
.week-day {
    min-height: 200px;
}

.week-header {
    padding: 0.5rem;
    font-weight: 600;
    text-align: center;
    border-bottom: 1px solid rgba(var(--b3) / 0.2);
}

.week-date {
    font-size: 1.5rem;
    font-weight: bold;
}

/* Day View Styles */
.day-view-container {
    min-height: 500px;
}

.day-view-header {
    padding: 1rem;
    border-bottom: 1px solid rgba(var(--b3) / 0.2);
    background-color: hsl(var(--b2));
}

.day-view-title {
    font-size: 1.5rem;
    font-weight: bold;
}

.day-view-content {
    padding: 1rem;
}

.day-view-container.is-today {
    border-left: 4px solid hsl(var(--p));
}

.day-view-header.bg-primary\/10 {
    border-bottom: 2px solid hsl(var(--p) / 0.4);
}

.week-day.is-today {
    border: 2px solid hsl(var(--p) / 0.6);
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
}

.is-today {
    background-color: hsl(var(--p) / 0.5) !important;
    border: 3px solid hsl(var(--p)) !important;
    position: relative;
}

.is-today::after {
    content: 'TODAY';
    position: absolute;
    top: 2px;
    right: 5px;
    font-size: 8px;
    font-weight: 800;
    letter-spacing: 0.5px;
    background-color: hsl(var(--p));
    color: hsl(var(--pc));
    padding: 2px 4px;
    border-radius: 3px;
    opacity: 0.9;
}

.current-month.is-today {
    background: linear-gradient(45deg, hsl(var(--a) / 0.75), hsl(var(--af) / 0.8)) !important;
    box-shadow: 0 4px 3px rgba(0, 0, 0, 0.2);
}

.is-today .day-header {
    background-color: hsl(var(--p) / 0.7);
    border-bottom: 2px solid hsl(var(--p));
    color: hsl(var(--pc));
}

.is-today .day-number {
    background-color: hsl(var(--p));
    color: hsl(var(--pc));
    font-weight: 700;
}

.week-day.is-today {
    background: linear-gradient(135deg, hsl(var(--p) / 0.65), hsl(var(--p) / 0.45)) !important;
    border: 3px solid hsl(var(--p)) !important;
}

.week-day.is-today .week-header {
    background-color: hsl(var(--p) / 0.7);
    color: hsl(var(--pc));
}

.day-view-container.is-today {
    background-color: hsl(var(--p) / 0.35) !important;
    border-left: 6px solid hsl(var(--p));
}

@media (max-width: 768px) {
    .calendar-container {
        min-width: 100%;
    }
    .day {
        min-width: 0;
    }
}

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
@endsection