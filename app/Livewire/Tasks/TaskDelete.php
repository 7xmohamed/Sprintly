<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Livewire\Component;

class TaskDelete extends Component
{
    public $taskId;
    public $taskTitle;
    
    public function mount($taskId = null)
    {
        // Check if user has permission to delete tasks
        if (!auth()->user()->hasRole(['director', 'supervisor'])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to delete tasks.'
            ]);
            $this->dispatch('closeModal');
            return;
        }

        if ($taskId) {
            $this->setTask($taskId);
        }
    }
    
    public function setTask($taskId)
    {
        $this->taskId = $taskId;
        $task = Task::findOrFail($taskId);
        $this->taskTitle = $task->title;
    }
    
    public function delete()
    {
        // Check if user has permission to delete tasks
        if (!auth()->user()->hasRole(['director', 'supervisor'])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to delete tasks.'
            ]);
            $this->dispatch('closeModal');
            return;
        }

        $task = Task::findOrFail($this->taskId);
        
        // Delete related records
        $task->taskAssignments()->delete();
        $task->taskComments()->delete();
        $task->delete();
        
        $this->dispatch('taskDeleted');
        $this->dispatch('closeModal');
        $this->dispatch('notify', [
            'message' => 'Task deleted successfully!',
            'type' => 'success',
        ]);
    }
    
    public function render()
    {
        return view('livewire.tasks.task-delete');
    }
} 