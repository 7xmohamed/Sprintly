<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $director = Role::create(['name' => 'director']);
        $supervisor = Role::create(['name' => 'supervisor']);
        $projectManager = Role::create(['name' => 'team_leader']);
        $employee = Role::create(['name' => 'employee']);

        // Create permissions
        $viewDashboard = Permission::create(['name' => 'view dashboard']);
        $manageApprovals = Permission::create(['name' => 'manage approvals']);
        $viewProjects = Permission::create(['name' => 'view projects']);
        $manageProjects = Permission::create(['name' => 'manage projects']);
        $viewTasks = Permission::create(['name' => 'view tasks']);
        $manageTasks = Permission::create(['name' => 'manage tasks']);

        // Assign permissions to roles
        $director->givePermissionTo([
            'view dashboard',
            'manage approvals',
            'view projects',
            'manage projects',
            'view tasks',
            'manage tasks'
        ]);

        $supervisor->givePermissionTo([
            'view dashboard',
            'manage approvals',
            'view projects',
            'manage projects',
            'view tasks',
            'manage tasks'
        ]);

        $projectManager->givePermissionTo([
            'view dashboard',
            'view projects',
            'manage projects',
            'view tasks',
            'manage tasks'
        ]);

        $employee->givePermissionTo([
            'view dashboard',
            'view projects',
            'view tasks'
        ]);
    }
} 