<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DeleteReportModal extends Component
{
    public $reportId;
    public $report;

    public function mount($reportId)
    {
        $this->reportId = $reportId;
        $this->report = DailyReport::with('user')->find($reportId);
    }

    public function delete()
    {
        if (!$this->report) {
            $this->dispatch('notify', [
                'message' => 'Report not found.',
                'type' => 'error',
            ]);
            return;
        }

        // Check if user has permission to delete this report
        if (Auth::id() !== $this->report->user_id && !Auth::user()->hasRole(['director', 'supervisor'])) {
            $this->dispatch('notify', [
                'message' => 'You are not authorized to delete this report.',
                'type' => 'error',
            ]);
            return;
        }

        $this->report->delete();
        
        $this->dispatch('reportDeleted');
        $this->dispatch('closeDeleteModal');
        $this->dispatch('notify', [
            'message' => 'Report deleted successfully!',
            'type' => 'success',
        ]);
    }

    public function close()
    {
        $this->dispatch('closeDeleteModal');
    }

    public function render()
    {
        return view('livewire.reports.delete-report-modal');
    }
} 