@extends('layout.app')

@section('title', 'Chats')

@section('content')
    @if(isset($projectId))
        <livewire:chat.chat-manager :project-id="$projectId" />
    @else
        <livewire:chat.chat-manager />
    @endif
@endsection
