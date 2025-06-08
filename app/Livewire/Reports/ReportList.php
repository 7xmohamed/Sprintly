<?php

namespace App\Livewire\Reports;

use App\Models\Project;
use App\Models\DailyReport;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportList extends Component
{
    use WithPagination;

    public $dateRange = 'today';
    public $projectFilter = '';
    public $departmentFilter = '';
    public $searchAssignee = '';
    public $showAssigneeModal = false;
    public $selectedAssigneeId = null;
    public $showEditModal = false;
    public $selectedReportId = null;
    public $showDeleteModal = false;
    public $reportToDeleteId = null;
    public $startDate;
    public $endDate;

    protected $queryString = [
        'dateRange' => ['except' => 'today'],
        'projectFilter' => ['except' => ''],
        'departmentFilter' => ['except' => ''],
        'searchAssignee' => ['except' => ''],
    ];

    protected $listeners = [
        'closeAssigneeModal' => 'closeAssigneeModal',
        'closeEditModal' => 'closeEditModal',
        'closeDeleteModal' => 'closeDeleteModal',
        'reportUpdated' => '$refresh',
        'reportDeleted' => '$refresh'
    ];

    public function mount()
    {
        $this->setDateRange('today');
    }

    protected function canModifyReport($report)
    {
        return Auth::id() === $report->user_id || 
               Auth::user()->hasRole(['director', 'supervisor']);
    }

    public function deleteReport($reportId)
    {
        $report = DailyReport::find($reportId);
        
        if (!$report) {
            $this->dispatch('notify', [
                'message' => 'Report not found.',
                'type' => 'error',
            ]);
            return;
        }

        if (!$this->canModifyReport($report)) {
            $this->dispatch('notify', [
                'message' => 'You are not authorized to delete this report.',
                'type' => 'error',
            ]);
            return;
        }
        
        $report->delete();
        
        $this->dispatch('notify', [
            'message' => 'Report deleted successfully!',
            'type' => 'success',
        ]);
    }

    public function setDateRange($range)
    {
        $this->dateRange = $range;
        $today = Carbon::today();

        switch ($range) {
            case 'today':
                $this->startDate = $today;
                $this->endDate = $today;
                break;
            case 'yesterday':
                $this->startDate = $today->subDay();
                $this->endDate = $this->startDate;
                break;
            case 'last_7_days':
                $this->startDate = $today->subDays(6);
                $this->endDate = $today;
                break;
            case 'last_30_days':
                $this->startDate = $today->subDays(29);
                $this->endDate = $today;
                break;
        }

        $this->resetPage();
    }

    public function showAssigneeReport($userId)
    {
        $this->selectedAssigneeId = $userId;
        $this->showAssigneeModal = true;
    }

    public function closeAssigneeModal()
    {
        $this->showAssigneeModal = false;
        $this->selectedAssigneeId = null;
    }

    public function showEditReport($reportId)
    {
        $report = DailyReport::find($reportId);
        
        if (!$report) {
            $this->dispatch('notify', [
                'message' => 'Report not found.',
                'type' => 'error',
            ]);
            return;
        }

        if (!$this->canModifyReport($report)) {
            $this->dispatch('notify', [
                'message' => 'You are not authorized to edit this report.',
                'type' => 'error',
            ]);
            return;
        }

        $this->selectedReportId = $reportId;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedReportId = null;
    }

    public function showDeleteReport($reportId)
    {
        $report = DailyReport::find($reportId);
        
        if (!$report) {
            $this->dispatch('notify', [
                'message' => 'Report not found.',
                'type' => 'error',
            ]);
            return;
        }

        if (!$this->canModifyReport($report)) {
            $this->dispatch('notify', [
                'message' => 'You are not authorized to delete this report.',
                'type' => 'error',
            ]);
            return;
        }

        $this->reportToDeleteId = $reportId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reportToDeleteId = null;
    }

    public function updatingSearchAssignee()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $reportsQuery = DailyReport::with(['user', 'user.departments', 'project']);

        // Filter reports based on user's role and project membership
        if (!auth()->user()->hasRole('director') && !auth()->user()->hasPermissionTo('view daily reports')) {
            $userProjectIds = auth()->user()->projectMembers()->pluck('project_id')->toArray();
            $reportsQuery->whereIn('project_id', $userProjectIds);
        }

        $reportsQuery->when($this->startDate && $this->endDate, function($query) {
                $query->whereBetween('date', [$this->startDate, $this->endDate]);
            })
            ->when($this->projectFilter, function($query) {
                $query->where('project_id', $this->projectFilter);
            })
            ->when($this->departmentFilter, function($query) {
                $query->whereHas('user.departments', function($q) {
                    $q->where('departments.id', $this->departmentFilter);
                });
            })
            ->when($this->searchAssignee, function($query) {
                $query->whereHas('user', function($q) {
                    $q->where(function($sq) {
                        $sq->where('first_name', 'like', '%' . $this->searchAssignee . '%')
                          ->orWhere('last_name', 'like', '%' . $this->searchAssignee . '%');
                    });
                });
            })
            ->orderBy('date', 'desc');

        // Filter project dropdown to only show projects user is a member of
        $projects = auth()->user()->hasPermissionTo('view reports') 
            ? Project::all()
            : Project::whereIn('id', auth()->user()->projectMembers()->pluck('project_id'))->get();

        return view('livewire.reports.report-list', [
            'reports' => $reportsQuery->paginate(10),
            'projects' => $projects,
            'departments' => Department::all(),
            'dateRangeOptions' => [
                'today' => 'Today (' . Carbon::today()->format('M d, Y') . ')',
                'yesterday' => 'Yesterday',
                'last_7_days' => 'Last 7 Days',
                'last_30_days' => 'Last 30 Days'
            ]
        ]);
    }
}
