<?php

namespace Winex\Sentinel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SentinelInstallCommand extends Command
{
    protected $signature = 'sentinel:install';
    protected $description = 'Install Sentinel package (publish migration with timestamp)';

    public function handle()
    {
        $packageMigration = __DIR__.'/../../database/migrations/create_sentinels_table.php';
        
        if (!File::exists($packageMigration)) {
            $this->error('Migration file not found in package.');
            return 1;
        }

        $timestamp = date('Y_m_d_His');
        $newFilename = $timestamp . '_create_sentinels_table.php';
        $targetPath = database_path('migrations/' . $newFilename);

        if (File::exists($targetPath)) {
            $this->error('Migration already exists: ' . $newFilename);
            return 1;
        }

        File::copy($packageMigration, $targetPath);
        $this->info('✓ Migration published: ' . $newFilename);
        $this->info('Run: php artisan migrate to create the sentinels table');
        
        return 0;
    }
}