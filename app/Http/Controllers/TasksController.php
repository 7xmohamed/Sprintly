<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Mail\TaskCompletedMail;
use Illuminate\Support\Facades\Mail;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not needed with Livewire
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not needed with Livewire
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not needed with Livewire
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        
        // Check if the task status was changed to completed
        if ($request->status === 'pending_approval' && $task->status !== 'pending_approval') {
            // Send email to supervisor
            Mail::to('kniptodati@gmail.com')->send(new TaskCompletedMail($task));
        }

        // Continue with the update process
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not needed with Livewire
    }
}
