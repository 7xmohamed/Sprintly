<div class="space-y-6">
    <!-- Profile Information -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title flex items-center gap-2">
                <span class="iconify w-6 h-6 text-primary" data-icon="solar:user-circle-bold-duotone"></span>
                Profile Information
            </h2>
            <p class="text-sm text-base-content/70">Update your account's profile information.</p>

            <form wire:submit="updateProfile" class="mt-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label" for="first_name">
                            <span class="label-text">First Name</span>
                        </label>
                        <input type="text" id="first_name" wire:model="first_name" class="input input-bordered w-full" required />
                        @error('first_name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label" for="last_name">
                            <span class="label-text">Last Name</span>
                        </label>
                        <input type="text" id="last_name" wire:model="last_name" class="input input-bordered w-full" required />
                        @error('last_name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-control w-full">
                    <label class="label" for="email">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="email" wire:model="email" class="input input-bordered w-full" required />
                    @error('email') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:diskette-bold-duotone"></span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Password -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title flex items-center gap-2">
                <span class="iconify w-6 h-6 text-primary" data-icon="solar:lock-password-bold-duotone"></span>
                Update Password
            </h2>
            <p class="text-sm text-base-content/70">Ensure your account is using a long, random password to stay secure.</p>

            <form wire:submit="updatePassword" class="mt-6 space-y-6">
                <div class="form-control w-full">
                    <label class="label" for="current_password">
                        <span class="label-text">Current Password</span>
                    </label>
                    <input type="password" id="current_password" wire:model="current_password" class="input input-bordered w-full" required />
                    @error('current_password') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control w-full">
                        <label class="label" for="password">
                            <span class="label-text">New Password</span>
                        </label>
                        <input type="password" id="password" wire:model="password" class="input input-bordered w-full" required />
                        @error('password') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label" for="password_confirmation">
                            <span class="label-text">Confirm Password</span>
                        </label>
                        <input type="password" id="password_confirmation" wire:model="password_confirmation" class="input input-bordered w-full" required />
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify w-5 h-5 mr-2" data-icon="solar:diskette-bold-duotone"></span>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    <div x-data="{ show: false, message: '' }"
         x-on:profile-updated.window="show = true; message = 'Profile updated successfully!'; setTimeout(() => show = false, 3000)"
         x-on:password-updated.window="show = true; message = 'Password updated successfully!'; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         class="alert alert-success shadow-lg fixed bottom-4 right-4 w-auto max-w-sm"
         style="display: none;">
        <div class="flex items-center gap-2">
            <span class="iconify w-6 h-6" data-icon="solar:check-circle-bold-duotone"></span>
            <span x-text="message"></span>
        </div>
    </div>
</div>
