<?php

namespace App\Livewire\Chat;

use App\Events\NewChatMessage;
use App\Models\Project;
use App\Models\ProjectsChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class ChatManager extends Component
{
    public $projects = [];
    public $currentProject = null;
    public $messages = [];
    public $newMessage = '';
    public $listeners = [];
    
    public function mount($projectId = null)
    {
        // Get all projects for directors, or only member projects for other users
        $user = Auth::user();
        if ($user->hasRole('director')) {
            $this->projects = Project::with('supervisedBy')->get()->toArray();
        } else {
        $this->projects = $user->projectMembers()->with('supervisedBy')->get()->toArray();
        }
        
        if ($projectId) {
            // If project ID is provided, try to find that project
            $project = Project::find($projectId);
            if ($project && ($user->hasRole('director') || in_array($project->id, array_column($this->projects, 'id')))) {
                // Make sure the user has access to this project
                $this->currentProject = $project->toArray();
                // Also get the supervisor
                if ($project->supervisedBy) {
                    $this->currentProject['supervisedBy'] = $project->supervisedBy->toArray();
                }
            } else {
                // Fallback to first project if the specified project doesn't exist
                // or the user doesn't have access
                $this->currentProject = $this->projects[0] ?? null;
            }
        } else {
            // Get the first project or null if no projects
            $this->currentProject = $this->projects[0] ?? null;
        }
        
        // If a project exists, get its chat messages
        if ($this->currentProject) {
            $this->loadMessages();
        }
    }
    
    public function getListeners()
    {
        $listeners = [
            'echo-private:chat.project.' . ($this->currentProject['id'] ?? 0) . '.new-message' => 'receiveMessage',
            'refreshChat' => 'refreshChat',
        ];
        
        return $listeners;
    }
    
    public function receiveMessage($event)
    {
        // Only process if we're on the right project
        if ($this->currentProject && $this->currentProject['id'] == $event['projectId']) {
            // Add the new message to our messages array
            $this->messages[] = $event['message'];
            
            // Scroll to bottom
            $this->dispatch('newMessageReceived');
        }
    }
    
    public function loadMessages()
    {
        $messages = ProjectsChatMessage::where('project_id', $this->currentProject['id'])
                                     ->with('user')
                                     ->orderBy('created_at')
                                     ->get();
        
        // Convert to array to avoid serialization issues
        $this->messages = $messages->map(function($message) {
            $data = $message->toArray();
            $data['user'] = $message->user->toArray();
            return $data;
        })->toArray();
        
        $this->dispatch('chatMessagesLoaded');
    }
    
    public function changeProject($projectId)
    {
        // Find the project in our cached projects list
        foreach ($this->projects as $project) {
            if ($project['id'] == $projectId) {
                $this->currentProject = $project;
                $this->loadMessages();
                return;
            }
        }
        
        // If not found in our list, fetch it and check permission
        $project = Project::with('supervisedBy')->find($projectId);
        if ($project && (Auth::user()->hasRole('director') || Auth::user()->projectMembers->contains('id', $project->id))) {
            $this->currentProject = $project->toArray();
            if ($project->supervisedBy) {
                $this->currentProject['supervisedBy'] = $project->supervisedBy->toArray();
            }
            $this->loadMessages();
        }
    }
    
    public function sendMessage()
    {
        // Validate the message
        $this->validate([
            'newMessage' => 'required|string'
        ]);
        
        // Create the message
        $chatMessage = ProjectsChatMessage::create([
            'project_id' => $this->currentProject['id'],
            'user_id' => Auth::id(),
            'message' => $this->newMessage
        ]);
        
        // Load the message with its user for broadcasting
        $chatMessage->load('user');
        
        // Format the message for broadcast
        $messageData = $chatMessage->toArray();
        $messageData['user'] = $chatMessage->user->toArray();
        
        // Broadcast to all users in this project
        event(new NewChatMessage($messageData, $this->currentProject['id']));
        
        // Clear the message input
        $this->newMessage = '';
        
        // Add the message to our local messages array
        $this->messages[] = $messageData;
        
        // Notify frontend to scroll down
        $this->dispatch('chatMessagesLoaded');
    }
    
    #[On('refreshChat')]
    public function refreshChat()
    {
        if ($this->currentProject) {
            $this->loadMessages();
        }
    }
    
    public function render()
    {
        return view('livewire.chat.chat-manager');
    }
} 