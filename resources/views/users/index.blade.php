@extends('layout.app')

@section('content')
<div>
    <div class="flex justify-between items-center mb-5">
        <h2 class="card-title">Users</h2>
        @if(auth()->user()->hasRole(['director', 'supervisor']))
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span> New User
        </a>
        @endif
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto bg-base-100 rounded-lg shadow-md">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th class="w-20">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="hover">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="w-10 rounded-full">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}" />
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleClass = [
                                    'director' => 'badge-warning',
                                    'supervisor' => 'badge-primary',
                                    'team_leader' => 'badge-success',
                                    'employee' => 'badge-info',
                                ][$user->role] ?? 'badge-ghost';
                            @endphp
                            <span class="badge badge-sm whitespace-nowrap capitalize {{ $roleClass }}">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->department->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-sm {{ $user->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                                    <span class="iconify w-5 h-5" data-icon="solar:menu-dots-bold-duotone"></span>
                                </div>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a href="{{ route('users.edit', $user) }}" data-edit-user>
                                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:pen-bold-duotone"></span> Edit
                                    </a></li>
                                    @if(auth()->user()->hasRole(['director', 'supervisor']))
                                    <li>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-error" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <span class="iconify w-5 h-5 mr-2" data-icon="solar:trash-bin-trash-bold-duotone"></span> Delete
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <div class="w-16 h-16 bg-neutral text-neutral-content rounded-full inline-flex items-center justify-center">
                                    <span class="iconify w-8 h-8" data-icon="solar:user-rounded-bold-duotone"></span>
                                </div>
                                <p class="text-gray-500">No users found</p>
                                @if(auth()->user()->hasRole(['director', 'supervisor']))
                                <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
                                    <span class="iconify w-5 h-5 mr-2" data-icon="solar:add-circle-bold-duotone"></span> Add User
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="pt-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
{{-- Modal JS removed: edit is now a normal page, not a modal window --}}
@endpush
