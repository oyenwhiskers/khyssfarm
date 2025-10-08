<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--name= : Custom backup name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        try {
            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            // Generate backup filename
            $backupName = $this->option('name') 
                ? $this->option('name') . '_' . Carbon::now()->format('Y-m-d_H-i-s')
                : 'khyss_farm_backup_' . Carbon::now()->format('Y-m-d_H-i-s');
            
            $backupFile = storage_path('backups/' . $backupName . '.sql');

            // Create mysqldump command
            $command = sprintf(
                'mysqldump -h%s -P%s -u%s -p%s %s > %s',
                $host,
                $port,
                $username,
                $password,
                $database,
                $backupFile
            );

            // Execute backup command
            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                $this->info("✅ Database backup created successfully!");
                $this->info("📁 Backup location: " . $backupFile);
                $this->info("💾 File size: " . $this->formatBytes(filesize($backupFile)));
                
                // Also create a JSON export for easy viewing
                $this->createJsonBackup($backupName);
                
                return Command::SUCCESS;
            } else {
                $this->error("❌ Backup failed with return code: " . $returnCode);
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Create a JSON backup for easy viewing
     */
    private function createJsonBackup($backupName)
    {
        try {
            $this->info('Creating JSON backup for easy viewing...');
            
            $backup = [
                'backup_info' => [
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'database' => config('database.connections.mysql.database'),
                    'laravel_version' => app()->version(),
                ],
                'data' => []
            ];

            // Get all table data
            $tables = ['harvests', 'sales', 'customers', 'costs', 'prices', 'marketings'];
            
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $backup['data'][$table] = DB::table($table)->get()->toArray();
                    $this->line("✓ Exported {$table} table (" . count($backup['data'][$table]) . " records)");
                }
            }

            $jsonFile = storage_path('backups/' . $backupName . '.json');
            file_put_contents($jsonFile, json_encode($backup, JSON_PRETTY_PRINT));
            
            $this->info("📄 JSON backup created: " . $jsonFile);
            
        } catch (\Exception $e) {
            $this->warn("⚠️  JSON backup failed: " . $e->getMessage());
        }
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
