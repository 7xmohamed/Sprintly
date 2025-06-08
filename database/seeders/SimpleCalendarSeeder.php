<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SimpleCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have at least one user
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create 5 projects using raw queries to avoid model fillable issues
        $projects = [
            ['name' => 'Website Redesign', 'description' => 'Revamp the company website with new branding and improved UX'],
            ['name' => 'Mobile App Development', 'description' => 'Build a new mobile app for our customers'],
            ['name' => 'Marketing Campaign', 'description' => 'Q3 Digital Marketing Campaign planning and execution'],
            ['name' => 'Product Launch', 'description' => 'New product launch activities and coordination'],
            ['name' => 'CRM Implementation', 'description' => 'Implement new CRM system and data migration']
        ];

        $projectStatuses = ['planning', 'in_progress', 'completed', 'on_hold'];
        $createdProjects = [];

        foreach ($projects as $projectData) {
            $projectId = DB::table('projects')->insertGetId([
                'name' => $projectData['name'],
                'description' => $projectData['description'],
                'created_by' => $user->id,
                'start_date' => Carbon::now()->subDays(rand(1, 30)),
                'end_date' => Carbon::now()->addDays(rand(30, 90)),
                'status' => $projectStatuses[array_rand($projectStatuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $createdProjects[] = (object)[
                'id' => $projectId,
                'name' => $projectData['name']
            ];
        }

        // Define priorities and statuses for tasks
        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending_approval', 'approved'];
        $currentStatuses = ['todo', 'in_progress', 'completed'];

        // Task titles by project
        $taskTitles = [
            'Website Redesign' => [
                'Homepage Redesign',
                'Contact Form Implementation',
                'Mobile Responsiveness',
                'SEO Optimization',
                'Content Migration'
            ],
            'Mobile App Development' => [
                'User Authentication',
                'Payment Gateway Integration',
                'Push Notifications',
                'Offline Mode Implementation',
                'Beta Testing'
            ],
            'Marketing Campaign' => [
                'Social Media Strategy',
                'Email Newsletter Design',
                'Analytics Setup',
                'Content Calendar',
                'Influencer Outreach'
            ],
            'Product Launch' => [
                'Press Release Drafting',
                'Demo Video Production',
                'Launch Event Planning',
                'Promotional Materials',
                'Customer Feedback Collection'
            ],
            'CRM Implementation' => [
                'Data Migration Plan',
                'User Training Schedule',
                'Integration Testing',
                'Custom Report Development',
                'Legacy System Decommissioning'
            ]
        ];

        // Create 20 tasks spread across the projects and dates
        $tasks = [];
        for ($i = 0; $i < 20; $i++) {
            $project = $createdProjects[array_rand($createdProjects)];
            $dueDate = Carbon::now()->addDays(rand(-5, 30)); // Some tasks in the past, some in the future
            
            // Get project-specific task titles
            $projectTaskTitles = $taskTitles[$project->name] ?? ["Task for {$project->name}"];
            $taskTitle = $projectTaskTitles[array_rand($projectTaskTitles)];
            
            $taskId = DB::table('tasks')->insertGetId([
                'title' => $taskTitle,
                'description' => "This is a detailed description for the task: {$taskTitle}",
                'project_id' => $project->id,
                'created_by' => $user->id,
                'due_date' => $dueDate,
                'priority' => $priorities[array_rand($priorities)],
                'current_status' => $currentStatuses[array_rand($currentStatuses)],
                'start_date' => Carbon::now()->subDays(rand(1, 15)),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $tasks[] = (object)['id' => $taskId];
        }

        // Assign all tasks to the user
        foreach ($tasks as $task) {
            DB::table('task_assignments')->insert([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'assigned_by' => $user->id,
                'assigned_at' => Carbon::now()->subDays(rand(1, 10)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Created 5 projects with 20 tasks, all assigned to the user.');
    }
} 