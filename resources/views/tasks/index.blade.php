@extends('layout.app')

@section('title', 'Tasks')

@section('content')
    <livewire:tasks.task-list />
    <livewire:modals.modal-manager />
@endsection