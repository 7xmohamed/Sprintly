@extends('layout.app')

@section('title', 'Dashboard Home')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:reports.create-report />
        </div>
    </div>
@endsection