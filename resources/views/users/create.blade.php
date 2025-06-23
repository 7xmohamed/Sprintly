@extends('layout.app')
@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Add User</h1>
    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1 font-medium">First Name</label>
            <input type="text" name="first_name" class="input input-bordered w-full" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Last Name</label>
            <input type="text" name="last_name" class="input input-bordered w-full" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Email</label>
            <input type="email" name="email" class="input input-bordered w-full" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Password</label>
            <input type="password" name="password" class="input input-bordered w-full" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" class="input input-bordered w-full" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Role</label>
            <select name="role" class="select select-bordered w-full" required>
                @foreach($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1 font-medium">Department</label>
            <select name="department_id" class="select select-bordered w-full" required>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-red font-semibold py-2 px-4 rounded">Create</button>
            <a href="{{ route('users.index') }}" class="bg-red-500 hover:bg-red-600 text-red font-semibold py-2 px-4 rounded">Cancel</a>
        </div>
    </form>
</div>
@endsection
