<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Responsible for managing and maintaining technology infrastructure',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Manages employee relations, recruitment, and workplace policies',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Handles financial planning, budgeting, and accounting',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Develops and implements marketing strategies',
                'is_active' => true,
            ],
            [
                'name' => 'Sales',
                'code' => 'SLS',
                'description' => 'Manages client relationships and revenue generation',
                'is_active' => true,
            ],
            [
                'name' => 'Research & Development',
                'code' => 'R&D',
                'description' => 'Focuses on innovation and product development',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Oversees day-to-day business operations',
                'is_active' => true,
            ],
            [
                'name' => 'Customer Service',
                'code' => 'CS',
                'description' => 'Provides support and assistance to customers',
                'is_active' => true,
            ],
            [
                'name' => 'Legal',
                'code' => 'LGL',
                'description' => 'Handles legal matters and compliance',
                'is_active' => true,
            ],
            [
                'name' => 'Quality Assurance',
                'code' => 'QA',
                'description' => 'Ensures product and service quality standards',
                'is_active' => true,
            ]
        ];

        foreach ($departments as $department) {
            // Create department
            Department::create($department);
        }

        // After creating departments, we can assign supervisors
        $departments = Department::all();
        
        // foreach ($departments as $department) {
        //     // Try to find a supervisor for this department
        //     $supervisor = User::where('role', 'supervisor')
        //                  ->whereNull('department_id')
        //                  ->inRandomOrder()
        //                  ->first();
            
        //     if ($supervisor) {
        //         $department->manager_id = $supervisor->id;
        //         $department->save();
                
        //         // Assign the supervisor to this department
        //         $supervisor->department_id = $department->id;
        //         $supervisor->save();
        //     }
        // }
    }
} 