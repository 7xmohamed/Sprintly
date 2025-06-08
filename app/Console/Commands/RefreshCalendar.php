<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the database and seed with calendar test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Refreshing database...');
        
        if (!app()->environment('production')) {
            // Confirm before proceeding
            if ($this->confirm('This will refresh your database. All existing data will be lost. Continue?', true)) {
                // Migrate fresh and seed
                $this->info('Migrating tables...');
                Artisan::call('migrate:fresh');
                $this->info('Tables migrated successfully.');
                
                $this->info('Seeding database...');
                Artisan::call('db:seed');
                $this->info('Database seeded successfully.');
                
                $this->info('Calendar data has been refreshed. The following test users have been created:');
                $this->table(
                    ['Name', 'Email', 'Password', 'Role'],
                    [
                        ['Admin User', 'admin@example.com', 'password', 'director'],
                        ['Project Manager', 'pm@example.com', 'password', 'team_leader'],
                        ['Team Member1', 'member1@example.com', 'password', 'employee'],
                        ['Team Member2', 'member2@example.com', 'password', 'employee'],
                    ]
                );
                
                return Command::SUCCESS;
            }
            
            $this->info('Operation cancelled.');
            return Command::FAILURE;
        }
        
        $this->error('This command should not be run in production!');
        return Command::FAILURE;
    }
}
