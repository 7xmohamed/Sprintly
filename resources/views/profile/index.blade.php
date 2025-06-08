@extends('layout.app')

@section('title', 'Profile Settings')

@section('content')
    <div class="max-w-7xl mx-auto py-6 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Profile Settings</h1>
        </div>

        @livewire('profile.update-profile')
    </div>
@endsection 