<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $showNotifications = false;
    public $notifications = [];

    protected $listeners = ['notificationMarkedAsRead' => 'refreshNotifications'];

    public function mount()
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount()
    {
        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
        if ($this->showNotifications) {
            $this->loadNotifications();
        }
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->with('from')
            ->latest()
            ->take(5)
            ->get();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
            $this->dispatch('notificationMarkedAsRead');
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        $this->loadUnreadCount();
        $this->loadNotifications();
    }

    #[On('notificationAdded')]
    public function refreshNotifications()
    {
        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    public function render()
    {
        // Only fetch unread notifications
        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->with('from')
            ->latest()
            ->take(5)
            ->get();

        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('livewire.notifications.notification-bell', [
            'notifications' => $notifications
        ]);
    }
} 