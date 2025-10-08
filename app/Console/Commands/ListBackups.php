<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class ListBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available database backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupsDir = storage_path('backups');
        
        if (!is_dir($backupsDir)) {
            $this->error("Backups directory not found: {$backupsDir}");
            return Command::FAILURE;
        }

        $sqlBackups = glob($backupsDir . '/*.sql');
        $jsonBackups = glob($backupsDir . '/*.json');
        
        if (empty($sqlBackups) && empty($jsonBackups)) {
            $this->info("No backup files found in {$backupsDir}");
            $this->info("Run 'php artisan db:backup' to create your first backup.");
            return Command::SUCCESS;
        }

        $this->info("📁 Available Database Backups:");
        $this->newLine();

        // Display SQL backups
        if (!empty($sqlBackups)) {
            $this->line("🗄️  SQL Backups (for restore):");
            
            $headers = ['#', 'Filename', 'Size', 'Created', 'Age'];
            $rows = [];
            
            foreach ($sqlBackups as $index => $backup) {
                $filename = basename($backup, '.sql');
                $size = $this->formatBytes(filesize($backup));
                $created = date('Y-m-d H:i:s', filemtime($backup));
                $age = Carbon::createFromTimestamp(filemtime($backup))->diffForHumans();
                
                $rows[] = [
                    $index + 1,
                    $filename,
                    $size,
                    $created,
                    $age
                ];
            }
            
            $this->table($headers, $rows);
        }

        // Display JSON backups
        if (!empty($jsonBackups)) {
            $this->newLine();
            $this->line("📄 JSON Backups (for viewing):");
            
            $headers = ['#', 'Filename', 'Size', 'Created', 'Age'];
            $rows = [];
            
            foreach ($jsonBackups as $index => $backup) {
                $filename = basename($backup, '.json');
                $size = $this->formatBytes(filesize($backup));
                $created = date('Y-m-d H:i:s', filemtime($backup));
                $age = Carbon::createFromTimestamp(filemtime($backup))->diffForHumans();
                
                $rows[] = [
                    $index + 1,
                    $filename,
                    $size,
                    $created,
                    $age
                ];
            }
            
            $this->table($headers, $rows);
        }

        $this->newLine();
        $this->info("💡 Usage:");
        $this->line("  • Create backup: php artisan db:backup");
        $this->line("  • Create named backup: php artisan db:backup --name=before_update");
        $this->line("  • Restore backup: php artisan db:restore");
        $this->line("  • Restore specific: php artisan db:restore backup_name");

        return Command::SUCCESS;
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
