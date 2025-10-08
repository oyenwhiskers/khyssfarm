<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {backup? : Backup file name (without extension)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from a backup file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('⚠️  WARNING: This will overwrite your current database!');
        
        if (!$this->confirm('Are you sure you want to continue?')) {
            $this->info('Restore cancelled.');
            return Command::SUCCESS;
        }

        try {
            $backupFile = $this->getBackupFile();
            
            if (!$backupFile) {
                return Command::FAILURE;
            }

            $this->info("Restoring database from: {$backupFile}");

            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            // Create mysql restore command
            $command = sprintf(
                'mysql -h%s -P%s -u%s -p%s %s < %s',
                $host,
                $port,
                $username,
                $password,
                $database,
                $backupFile
            );

            // Execute restore command
            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                $this->info("✅ Database restored successfully!");
                $this->info("🔄 Please run 'php artisan migrate:status' to verify tables.");
                return Command::SUCCESS;
            } else {
                $this->error("❌ Restore failed with return code: " . $returnCode);
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("❌ Restore failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Get backup file path
     */
    private function getBackupFile()
    {
        $backupsDir = storage_path('backups');
        
        if (!is_dir($backupsDir)) {
            $this->error("Backups directory not found: {$backupsDir}");
            return null;
        }

        $backupName = $this->argument('backup');
        
        if ($backupName) {
            $backupFile = $backupsDir . '/' . $backupName . '.sql';
            
            if (!file_exists($backupFile)) {
                $this->error("Backup file not found: {$backupFile}");
                return null;
            }
            
            return $backupFile;
        }

        // List available backups
        $backups = glob($backupsDir . '/*.sql');
        
        if (empty($backups)) {
            $this->error("No backup files found in {$backupsDir}");
            return null;
        }

        $this->info("Available backups:");
        $options = [];
        
        foreach ($backups as $index => $backup) {
            $filename = basename($backup, '.sql');
            $size = $this->formatBytes(filesize($backup));
            $date = date('Y-m-d H:i:s', filemtime($backup));
            
            $this->line(($index + 1) . ". {$filename} ({$size}) - {$date}");
            $options[$index + 1] = $backup;
        }

        $choice = $this->ask('Select backup number to restore');
        
        if (!isset($options[$choice])) {
            $this->error("Invalid selection.");
            return null;
        }

        return $options[$choice];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
