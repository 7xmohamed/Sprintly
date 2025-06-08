<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectsChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Simply return the view, Livewire component will handle the rest
        return view('chats.index');
    }

    /**
     * Display the specified project's chat.
     */
    public function show(string $id)
    {
        // Just return the index view with the project ID
        return view('chats.index', ['projectId' => $id]);
    }

    /**
     * These methods are left for API compatibility, but 
     * functionality has been moved to the Livewire component
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Functionality moved to Livewire component
        return redirect()->back();
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
