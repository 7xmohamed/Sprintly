@extends('layout.app')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
        <span class="iconify w-6 h-6 text-primary" data-icon="solar:user-pen-bold-duotone"></span>
        Edit User
    </h2>
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PATCH')
        <div class="mb-4">
            <label for="first_name" class="block text-sm font-medium mb-1">First Name</label>
            <input type="text" id="first_name" name="first_name" class="input input-bordered w-full" value="{{ old('first_name', $user->first_name) }}" required>
            @error('first_name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label for="last_name" class="block text-sm font-medium mb-1">Last Name</label>
            <input type="text" id="last_name" name="last_name" class="input input-bordered w-full" value="{{ old('last_name', $user->last_name) }}" required>
            @error('last_name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" id="email" name="email" class="input input-bordered w-full" value="{{ old('email', $user->email) }}" required>
            @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium mb-1">Role</label>
            <select id="role" name="role" class="select select-bordered w-full" required>
                <option value="" disabled>Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $user->role) == $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            @error('role') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="mb-6">
            <label for="department_id" class="block text-sm font-medium mb-1">Department</label>
            <select id="department_id" name="department_id" class="select select-bordered w-full" required>
                <option value="" disabled>Select department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('department_id', $user->department_id) == $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
            @error('department_id') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('users.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
        @if(session('success'))
            <div class="alert alert-success mt-4">{{ session('success') }}</div>
        @endif
    </form>
</div>
@endsection
