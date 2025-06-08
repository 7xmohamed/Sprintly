<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TasksController;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Livewire\Projects\ProjectList;
use App\Livewire\Projects\ProjectDetails;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Guest routes (only accessible when NOT logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Protected routes (only accessible when logged in)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', ProjectsController::class);
    Route::resource('tasks', TasksController::class);
    Route::resource('calendar', CalendarController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('chats', ChatsController::class);
    // Authentication
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    Route::post('/notifications/{notification}/mark-as-read', function (\App\Models\Notification $notification) {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
        }
        return back();
    })->name('notifications.markAsRead');
});

Route::get('/email', function () {
    Mail::to('kniptodati@gmail.com')->send(new TestMail());
    return 'Email has been sent!';
});

require __DIR__.'/auth.php';
