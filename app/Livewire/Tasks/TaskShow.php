<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Mail\TaskCompletedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;

class TaskShow extends Component
{
    public $taskId;
    public $task;
    public $comment = '';
    
    protected $rules = [
        'comment' => 'required|string|max:1000',
    ];
    
    public function mount($taskId)
    {
        $this->taskId = $taskId;
        $this->loadTask();
    }
    
    public function loadTask()
    {
        $this->task = Task::with(['project', 'createdBy', 'taskAssignments.user', 'taskComments.user', 'repetitiveTask'])
            ->findOrFail($this->taskId);
    }
    
    public function addComment()
    {
        $this->validate();
        
        TaskComment::create([
            'task_id' => $this->taskId,
            'user_id' => Auth::id(),
            'text' => $this->comment,
        ]);
        
        $this->comment = '';
        $this->loadTask();
    }
    
    public function deleteComment($commentId)
    {
        $comment = TaskComment::findOrFail($commentId);
        
        // Only allow deletion if the user is the comment author or has special permissions
        if ($comment->user_id === Auth::id()) {
            $comment->delete();
            $this->loadTask();
            
            $this->dispatch('notify', [
                'message' => 'Comment deleted successfully!',
                'type' => 'success',
            ]);
        }
    }
    
    public function updateStatus($status)
    {
        $task = Task::findOrFail($this->taskId);
        $task->update(['current_status' => $status]);
        
        // Send email when task is marked as completed
        if ($status === 'completed') {
            Mail::to('kniptodati@gmail.com')->send(new TaskCompletedMail($task));

            // Get the project supervisor ID from supervised_by column
            $supervisorId = $task->project->supervised_by;
            if ($supervisorId) {
                try {
                    Notification::create([
                        'user_id' => $supervisorId,
                        'from_id' => auth()->id(),
                        'title' => 'Task Completed',
                        'message' => "Task '{$task->title}' has been marked as completed and requires your approval",
                        'type' => 'status_change',
                        'data' => [
                            'task_id' => $task->id,
                            'task_title' => $task->title,
                            'project_id' => $task->project_id,
                            'completed_by' => auth()->user()->name
                        ],
                        'is_read' => false
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create notification: ' . $e->getMessage());
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Failed to send notification to supervisor'
                    ]);
                }
            }
        }
        
        $this->loadTask();
        $this->dispatch('taskUpdated');
        $this->dispatch('notify', [
            'message' => 'Task status updated successfully!',
            'type' => 'success',
        ]);
    }
    
    public function openEditModal()
    {
        $params = [
            'component' => 'tasks.task-edit',
            'arguments' => [
                'taskId' => $this->taskId
            ]
        ];
        $this->dispatch('openModal', $params);
    }
    
    public function updateApprovalStatus($status)
    {
        if (!auth()->user()->hasRole(['director', 'supervisor'])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to change the approval status.'
            ]);
            return;
        }

        if ($this->task->current_status !== 'completed') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Only completed tasks can have their approval status changed.'
            ]);
            return;
        }

        try {
            $oldStatus = $this->task->status;
            $statusMessage = '';

            if ($status === 'in_progress') {
                // If returning to progress, update both status and current_status
                $this->task->current_status = 'in_progress';
                $this->task->status = 'pending_approval';
                $this->task->save();
                
                $statusMessage = 'Task has been returned to progress.';
            } else {
                // For approval status changes
                $this->task->status = $status;
                $this->task->save();
                
                $statusMessage = $status === 'approved' ? 'Task has been approved.' : 'Task is pending approval.';
            }

            // Notify all assigned members about the status change
            foreach ($this->task->taskAssignments as $assignment) {
                if ($assignment->user_id != auth()->id()) { // Don't notify the supervisor making the change
                    Notification::create([
                        'user_id' => $assignment->user_id,
                        'from_id' => auth()->id(),
                        'title' => 'Task Status Updated',
                        'message' => "Task '{$this->task->title}' has been " . 
                            ($status === 'approved' ? 'approved' : 
                            ($status === 'in_progress' ? 'returned to progress' : 'marked as pending approval')),
                        'type' => 'status_change',
                        'data' => [
                            'task_id' => $this->task->id,
                            'task_title' => $this->task->title,
                            'project_id' => $this->task->project_id,
                            'old_status' => $oldStatus,
                            'new_status' => $status,
                            'updated_by' => auth()->user()->name
                        ],
                        'is_read' => false
                    ]);
                }
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $statusMessage
            ]);

            // Refresh the task data
            $this->task->refresh();
            
            // Emit event for task update
            $this->dispatch('taskUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error updating task status: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.tasks.task-show');
    }
} 