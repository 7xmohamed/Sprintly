<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateCalendarTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:create-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates test projects and tasks for the calendar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test data for the calendar...');
        
        // Get the first user
        $user = User::first();
        
        if (!$user) {
            $this->error('No user found. Please create a user first.');
            return 1;
        }
        
        // First, let's check the structure of the projects table
        $this->info('Checking projects table structure...');
        $projectColumns = DB::select('SHOW COLUMNS FROM projects');
        $projectColumnNames = array_map(fn($col) => $col->Field, $projectColumns);
        $this->info('Project columns: ' . implode(', ', $projectColumnNames));
        
        // Same for tasks
        $this->info('Checking tasks table structure...');
        $taskColumns = DB::select('SHOW COLUMNS FROM tasks');
        $taskColumnNames = array_map(fn($col) => $col->Field, $taskColumns);
        $this->info('Task columns: ' . implode(', ', $taskColumnNames));
        
        // First, check if we need to create a department
        $this->info('Checking if we need to create a department...');
        $departmentId = DB::table('departments')->insertGetId([
            'name' => 'Engineering',
            'description' => 'Engineering department for testing',
            'manager_id' => $user->id,
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->info("Created department with ID: {$departmentId}");
        
        // Project data
        $projects = [
            ['name' => 'Website Redesign', 'description' => 'Revamp the company website with new branding and improved UX'],
            ['name' => 'Mobile App Development', 'description' => 'Build a new mobile app for our customers'],
            ['name' => 'Marketing Campaign', 'description' => 'Q3 Digital Marketing Campaign planning and execution'],
            ['name' => 'Product Launch', 'description' => 'New product launch activities and coordination'],
            ['name' => 'CRM Implementation', 'description' => 'Implement new CRM system and data migration']
        ];
        
        // Create projects dynamically based on the columns we have
        $this->info('Creating projects...');
        $createdProjects = [];
        $statuses = ['planning', 'in_progress', 'completed', 'on_hold'];
        
        foreach ($projects as $projectData) {
            $data = [
                'name' => $projectData['name'],
                'description' => $projectData['description'],
                'created_by' => $user->id,
                'start_date' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(rand(30, 90))->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add manager_id if it exists in the table
            if (in_array('manager_id', $projectColumnNames)) {
                $data['manager_id'] = $user->id;
            }
            
            // Add department_id if it exists in the table
            if (in_array('department_id', $projectColumnNames)) {
                $data['department_id'] = $departmentId;
            }
            
            // Check if supervised_by exists and add it
            if (in_array('supervised_by', $projectColumnNames)) {
                $data['supervised_by'] = $user->id;
            }
            
            $this->info('Inserting project with data: ' . json_encode($data));
            $projectId = DB::table('projects')->insertGetId($data);
            
            $createdProjects[] = (object)[
                'id' => $projectId,
                'name' => $projectData['name']
            ];
            
            $this->info("Created project: {$projectData['name']}");
        }
        
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
        
        // Define priorities and statuses for tasks
        $priorities = ['high', 'medium', 'low'];
        $statuses = ['todo', 'in_progress', 'completed'];
        $currentStatuses = ['todo', 'in_progress', 'completed'];
        
        // Create tasks
        $this->info('Creating tasks...');
        $tasks = [];
        
        for ($i = 0; $i < 20; $i++) {
            $project = $createdProjects[array_rand($createdProjects)];
            $dueDate = Carbon::now()->addDays(rand(-5, 30))->format('Y-m-d');
            
            // Get project-specific task titles
            $projectTaskTitles = $taskTitles[$project->name] ?? ["Task for {$project->name}"];
            $taskTitle = $projectTaskTitles[array_rand($projectTaskTitles)];
            
            $taskData = [
                'title' => $taskTitle,
                'description' => "This is a detailed description for the task: {$taskTitle}",
                'project_id' => $project->id,
                'created_by' => $user->id,
                'due_date' => $dueDate,
                'priority' => $priorities[array_rand($priorities)],
                'current_status' => $currentStatuses[array_rand($currentStatuses)],
                'start_date' => Carbon::now()->subDays(rand(1, 15))->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $taskId = DB::table('tasks')->insertGetId($taskData);
            
            $tasks[] = (object)['id' => $taskId, 'title' => $taskTitle];
            
            $this->info("Created task: {$taskTitle}");
        }
        
        // Assign tasks to the user
        $this->info('Assigning tasks to user...');
        
        foreach ($tasks as $task) {
            DB::table('task_assignments')->insert([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'assigned_by' => $user->id,
                'assigned_at' => Carbon::now()->subDays(rand(1, 10)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info("Assigned task '{$task->title}' to user.");
        }
        
        $this->info('Test data creation completed successfully!');
        $this->info('Created ' . count($createdProjects) . ' projects and ' . count($tasks) . ' tasks.');
        
        return 0;
    }
} 