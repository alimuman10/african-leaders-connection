<?php

namespace App\Console\Commands;

use Database\Seeders\SuperAdminSeeder;
use Illuminate\Console\Command;

class BootstrapSuperAdmin extends Command
{
    protected $signature = 'auth:bootstrap-super-admin';

    protected $description = 'Create the protected Super Admin from SUPER_ADMIN_PASSWORD in .env.';

    public function handle(): int
    {
        $this->call('db:seed', [
            '--class' => SuperAdminSeeder::class,
            '--force' => true,
        ]);

        $this->components->info('Super Admin bootstrap completed. Rotate the initial password immediately after deployment.');

        return self::SUCCESS;
    }
}
