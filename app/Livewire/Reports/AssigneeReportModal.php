<?php

namespace App\Livewire\Reports;

use App\Models\User;
use App\Models\DailyReport;
use Livewire\Component;
use Carbon\Carbon;

class AssigneeReportModal extends Component
{
    public $userId;
    public $user;
    public $reports = [];
    public $dateRange = 'today';
    public $startDate;
    public $endDate;

    protected $listeners = [];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::with('departments')->find($userId);
        $this->setDateRange('today');
        $this->loadReports();
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

        $this->loadReports();
    }

    public function loadReports()
    {
        $this->reports = DailyReport::with(['project'])
            ->where('user_id', $this->userId)
            ->when($this->startDate && $this->endDate, function($query) {
                $query->whereBetween('date', [$this->startDate, $this->endDate]);
            })
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($report) {
                return [
                    'id' => $report->id,
                    'date' => $report->date,
                    'summary' => $report->summary,
                    'submitted_at' => $report->submitted_at,
                    'project' => [
                        'id' => $report->project->id,
                        'name' => $report->project->name
                    ]
                ];
            });
    }

    public function render()
    {
        return view('livewire.reports.assignee-report-modal', [
            'dateRangeOptions' => [
                'today' => 'Today (' . Carbon::today()->format('M d, Y') . ')',
                'yesterday' => 'Yesterday',
                'last_7_days' => 'Last 7 Days',
                'last_30_days' => 'Last 30 Days'
            ]
        ]);
    }
} 