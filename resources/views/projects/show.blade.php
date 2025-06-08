@extends('layout.app')

@section('title', 'Projects')

@section('content')
    <livewire:projects.project-details :project="$project" />
@endsection 