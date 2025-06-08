@extends('layout.app')

@section('title', 'Notifications')

@section('content')
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <livewire:notifications.notification-list />
        </div>
    </div>
@endsection