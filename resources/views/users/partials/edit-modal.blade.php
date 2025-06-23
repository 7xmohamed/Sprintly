<div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="relative bg-white rounded-xl shadow-2xl border border-blue-200 w-full max-w-md mx-2 animate-fade-in">
        <button id="closeEditModal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl leading-none">&times;</button>
        <div class="px-6 pt-6 pb-2 border-b border-gray-100 flex items-center gap-2">
            <span class="iconify w-6 h-6 text-primary" data-icon="solar:user-pen-bold-duotone"></span>
            <h2 class="text-lg font-semibold text-gray-800 mb-0">Edit User</h2>
        </div>
        <div class="p-6" id="editUserFormContainer">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 gap-4">
                    <div class="form-control">
                        <label class="label" for="first_name">
                            <span class="label-text">First Name</span>
                        </label>
                        <input type="text" id="first_name" class="input input-bordered w-full" name="first_name" value="{{ $user->first_name }}" required />
                        @error('first_name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label" for="last_name">
                            <span class="label-text">Last Name</span>
                        </label>
                        <input type="text" id="last_name" class="input input-bordered w-full" name="last_name" value="{{ $user->last_name }}" required />
                        @error('last_name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label" for="email">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" id="email" class="input input-bordered w-full" name="email" value="{{ $user->email }}" required />
                        @error('email') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label" for="role">
                            <span class="label-text">Role</span>
                        </label>
                        <select id="role" class="select select-bordered w-full" name="role" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" @selected($user->role == $role)>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label" for="department_id">
                            <span class="label-text">Department</span>
                        </label>
                        <select id="department_id" class="select select-bordered w-full" name="department_id" required>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected($user->department_id == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-6">
                    <button type="button" class="btn" onclick="document.getElementById('editUserModal').classList.add('hidden')">
                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:close-circle-bold-duotone"></span>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:disk-bold-duotone"></span>
                        Update User
                    </button>
                </div>
                @if(session('success'))
                    <div class="alert alert-success mt-4">User updated successfully</div>
                @endif
            </form>
        </div>
    </div>
</div>
