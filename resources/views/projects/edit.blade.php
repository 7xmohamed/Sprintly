@extends('layout.app')

@section('title', 'Edit Project')

@section('content')
    <div class="max-w-7xl mx-auto">
        <livewire:edit-project :project="$project" />
    </div>
@endsection 