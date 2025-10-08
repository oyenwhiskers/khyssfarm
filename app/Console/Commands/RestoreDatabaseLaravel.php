<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestoreDatabaseLaravel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore-laravel {backup? : Backup file name (without extension)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from a Laravel JSON backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('⚠️  WARNING: This will overwrite your current database data!');
        
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

            // Load backup data
            $backupData = json_decode(file_get_contents($backupFile), true);
            
            if (!$backupData) {
                $this->error("Invalid backup file format.");
                return Command::FAILURE;
            }

            $this->info("Backup created: " . $backupData['backup_info']['created_at']);
            
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Restore data
            foreach ($backupData['data'] as $tableName => $records) {
                if (!Schema::hasTable($tableName)) {
                    $this->warn("Table {$tableName} doesn't exist, skipping...");
                    continue;
                }

                // Clear existing data
                DB::table($tableName)->truncate();
                
                // Insert backup data
                if (!empty($records)) {
                    // Convert objects to arrays
                    $records = array_map(function($record) {
                        return (array) $record;
                    }, $records);
                    
                    // Insert in chunks to avoid memory issues
                    $chunks = array_chunk($records, 100);
                    foreach ($chunks as $chunk) {
                        DB::table($tableName)->insert($chunk);
                    }
                    
                    $this->line("✓ Restored {$tableName}: " . count($records) . " records");
                } else {
                    $this->line("✓ {$tableName}: No records to restore");
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $this->info("✅ Database restored successfully!");
            $this->info("🔄 Your KHYSS Farm data has been recovered.");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Restore failed: " . $e->getMessage());
            
            // Re-enable foreign key checks on error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            } catch (\Exception $e2) {
                // Ignore
            }
            
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
            $backupFile = $backupsDir . '/' . $backupName . '.json';
            
            if (!file_exists($backupFile)) {
                $this->error("Backup file not found: {$backupFile}");
                return null;
            }
            
            return $backupFile;
        }

        // List available JSON backups
        $backups = glob($backupsDir . '/*.json');
        
        if (empty($backups)) {
            $this->error("No JSON backup files found in {$backupsDir}");
            return null;
        }

        $this->info("Available backups:");
        $options = [];
        
        foreach ($backups as $index => $backup) {
            $filename = basename($backup, '.json');
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