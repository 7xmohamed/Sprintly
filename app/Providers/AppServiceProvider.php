<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Livewire\Livewire::component('tasks.task-list', \App\Livewire\Tasks\TaskList::class);
        \Livewire\Livewire::component('tasks.task-create', \App\Livewire\Tasks\TaskCreate::class);
        \Livewire\Livewire::component('tasks.task-edit', \App\Livewire\Tasks\TaskEdit::class);
        \Livewire\Livewire::component('tasks.task-show', \App\Livewire\Tasks\TaskShow::class);
        \Livewire\Livewire::component('tasks.task-delete', \App\Livewire\Tasks\TaskDelete::class);
        \Livewire\Livewire::component('tasks.create-task-page', \App\Livewire\Tasks\CreateTaskPage::class);
        \Livewire\Livewire::component('modals.modal-manager', \App\Livewire\Modals\ModalManager::class);
        \Livewire\Livewire::component('notification-manager', \App\Livewire\NotificationManager::class);
        \Livewire\Livewire::component('chat.chat-manager', \App\Livewire\Chat\ChatManager::class);
    }
}
