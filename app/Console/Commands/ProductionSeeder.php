<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProductionSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed:production {--force : Force seeding even in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with production-safe data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('This command is running in production mode!');
            $this->warn('Use --force flag to override this safety check.');
            $this->info('Example: php artisan db:seed:production --force');
            return 1;
        }

        $this->info('Starting production seeding...');

        try {
            // Run RolePermissionSeeder first
            $this->info('Seeding roles and permissions...');
            Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
            $this->info('✓ Roles and permissions seeded successfully!');

            // Run AssetManagementSeeder
            $this->info('Seeding asset management data...');
            Artisan::call('db:seed', ['--class' => 'AssetManagementSeeder']);
            $this->info('✓ Asset management data seeded successfully!');

            $this->info('Production seeding completed successfully!');
            $this->info('Super Admin: admin@assets.com / password');

        } catch (\Exception $e) {
            $this->error('Seeding failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
