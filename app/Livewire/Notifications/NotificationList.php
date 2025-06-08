<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layout.app')]
class NotificationList extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $search = '';

    protected $queryString = [
        'filter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->delete();
        }
    }

    public function render()
    {
        $query = Notification::where('user_id', auth()->id())
            ->with('from')
            ->when($this->filter === 'unread', function ($query) {
                return $query->where('is_read', false);
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('message', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('is_read', 'asc') // Show unread notifications first
            ->orderBy('created_at', 'desc'); // Then sort by date

        $notifications = $query->paginate(10);

        return view('livewire.notifications.notification-list', [
            'notifications' => $notifications,
        ]);
    }
} 