<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Project Management
            'view all projects',
            'view assigned projects',
            'create projects',
            'edit projects',
            'delete projects',
            'assign projects',
            'update project status',
            
            // Task Management
            'view all tasks',
            'view assigned tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',
            'update task status',
            
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Department Management
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',
            
            // Reports
            'view reports',
            'generate reports',
            
            // Settings
            'manage settings',
            
            // Chat/Communication
            'view chat',
            'send messages',
            
            // Daily Reports
            'view daily reports',
            'create daily reports',
            'edit daily reports',
            
            // Notifications
            'view notifications',
            'manage notifications'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $director = Role::create(['name' => 'director']);
        $director->givePermissionTo([
            'view all projects', 'create projects', 'edit projects', 'delete projects', 'assign projects', 'update project status',
            'view all tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks', 'update task status',
            'view users', 'create users', 'edit users', 'delete users',
            'view departments', 'create departments', 'edit departments', 'delete departments',
            'view reports', 'generate reports',
            'manage settings',
            'view chat', 'send messages',
            'view daily reports', 'create daily reports', 'edit daily reports',
            'view notifications', 'manage notifications'
        ]);

        $supervisor = Role::create(['name' => 'supervisor']);
        $supervisor->givePermissionTo([
            'view assigned projects', 'assign projects', 'update project status',
            'view all tasks', 'create tasks', 'edit tasks', 'assign tasks', 'update task status',
            'view users',
            'view departments',
            'view reports', 'generate reports',
            'view chat', 'send messages',
            'view daily reports', 'create daily reports', 'edit daily reports',
            'view notifications'
        ]);

        $team_leader = Role::create(['name' => 'team_leader']);
        $team_leader->givePermissionTo([
            'view assigned projects',
            'view assigned tasks', 'update task status',
            'view users',
            'view departments',
            'view reports', 'generate reports',
            'view chat', 'send messages',
            'view daily reports', 'create daily reports', 'edit daily reports',
            'view notifications'
        ]);
        
        

        $employee = Role::create(['name' => 'employee']);
        $employee->givePermissionTo([
            'view assigned projects',
            'view assigned tasks',
            'update task status',
            'view departments',
            'view reports',
            'view chat', 'send messages',
            'view daily reports', 'create daily reports',
            'view notifications'
        ]);
    }
} 