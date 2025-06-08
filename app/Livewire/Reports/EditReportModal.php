<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use App\Models\Project;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class EditReportModal extends Component
{
    public $reportId;
    public $report;
    public $date;
    public $summary;
    public $project_id;
    public $availableProjects = [];

    protected $rules = [
        'date' => 'required|date',
        'summary' => 'required|string',
        'project_id' => 'required|exists:projects,id'
    ];

    public function mount($reportId)
    {
        $this->reportId = $reportId;
        $this->report = DailyReport::find($reportId);
        
        if ($this->report) {
            $this->date = $this->report->date->format('Y-m-d');
            $this->summary = $this->report->summary;
            $this->project_id = $this->report->project_id;
        }
        
        $this->loadAvailableProjects();
    }

    public function loadAvailableProjects()
    {
        $this->availableProjects = Project::whereHas('members', function($query) {
            $query->where('user_id', $this->report->user_id);
        })->get();
    }

    public function save()
    {
        $this->validate();

        // Check if a report already exists for this date and project (excluding current report)
        $existingReport = DailyReport::where('user_id', $this->report->user_id)
            ->whereDate('date', $this->date)
            ->where('id', '!=', $this->report->id)
            ->first();

        if ($existingReport) {
            $this->addError('date', 'You have already submitted a report for this date.');
            return;
        }

        try {
            $this->report->update([
                'date' => $this->date,
                'summary' => $this->summary,
                'project_id' => $this->project_id,
            ]);

            $project = Project::with('supervisedBy')->find($this->project_id);
            
            // Send notification to supervisor if exists
            if ($project && $project->supervisedBy) {
                Notification::create([
                    'user_id' => $project->supervisedBy->id,
                    'from_id' => auth()->id(),
                    'title' => 'Daily Report Updated',
                    'message' => auth()->user()->name . ' has updated a daily report for project: ' . $project->name,
                    'type' => 'reminder',
                    'data' => [
                        'report_id' => $this->report->id,
                        'project_id' => $project->id,
                        'project_name' => $project->name,
                        'submitted_by' => auth()->user()->name
                    ],
                    'is_read' => false
                ]);
            }
            $this->dispatch('reportUpdated');
            $this->dispatch('closeEditModal');
            $this->dispatch('notify', [
                'message' => 'Report updated successfully!',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Failed to update report. Please try again.',
                'type' => 'error',
            ]);
        }
    }

    public function close()
    {
        $this->dispatch('closeEditModal');
    }

    public function render()
    {
        return view('livewire.reports.edit-report-modal');
    }
} 