<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create supervisors (one for each department plus some extra)
        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'email' => 'supervisor' . $i . '@example.com',
                'password' => Hash::make('password'),
                'first_name' => 'Supervisor',
                'last_name' => 'User ' . $i,
                'role' => 'supervisor',
                'is_active' => true,
                'department_id' => rand(1, 10), 
            ]);
        }

        // Create project managers
        // for ($i = 1; $i <= 20; $i++) {
        //     User::create([
        //         'username' => 'pm' . $i,
        //         'email' => 'pm' . $i . '@example.com',
        //         'password' => Hash::make('password'),
        //         'first_name' => 'Project',
        //         'last_name' => 'Manager ' . $i,
        //         'role' => 'team_leader',
        //         'is_active' => true,
        //         'department_id' => rand(1, 10), 
        //     ]);
        // }

        // Create regular employees
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'email' => 'employee' . $i . '@example.com',
                'password' => Hash::make('password'),
                'first_name' => 'Employee',
                'last_name' => 'User ' . $i,
                'role' => 'employee',
                'is_active' => true,
                'department_id' => rand(1, 10), 
            ]);
        }
    }
}