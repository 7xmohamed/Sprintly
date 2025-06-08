<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{
    public static function getAvailableRoles()
    {
        // Exclude any roles you don't want to show in registration
        return Role::whereNotIn('name', ['director'])
            ->pluck('name', 'name')
            ->map(function ($role) {
                return ucfirst(str_replace('_', ' ', $role));
            });
    }
} 