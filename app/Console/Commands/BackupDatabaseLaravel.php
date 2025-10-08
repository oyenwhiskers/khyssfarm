<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BackupDatabaseLaravel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-laravel {--name= : Custom backup name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Laravel-based backup of the database (no mysqldump required)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Laravel database backup...');

        try {
            // Generate backup filename
            $backupName = $this->option('name') 
                ? $this->option('name') . '_' . Carbon::now()->format('Y-m-d_H-i-s')
                : 'khyss_farm_backup_' . Carbon::now()->format('Y-m-d_H-i-s');
            
            $this->info("Creating backup: {$backupName}");

            // Create comprehensive backup
            $backup = [
                'backup_info' => [
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'database' => config('database.connections.mysql.database'),
                    'laravel_version' => app()->version(),
                    'backup_type' => 'Laravel Full Backup',
                    'php_version' => PHP_VERSION,
                ],
                'schema' => $this->getSchemaInfo(),
                'data' => $this->getAllData()
            ];

            // Save JSON backup
            $jsonFile = storage_path('backups/' . $backupName . '.json');
            file_put_contents($jsonFile, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Create SQL backup
            $this->createSqlBackup($backup, $backupName);

            $this->info("✅ Database backup created successfully!");
            $this->info("📄 JSON backup: " . $jsonFile);
            $this->info("💾 Backup size: " . $this->formatBytes(filesize($jsonFile)));
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Get schema information
     */
    private function getSchemaInfo()
    {
        $schema = [];
        $tables = DB::select('SHOW TABLES');
        $databaseName = config('database.connections.mysql.database');
        
        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$databaseName}"};
            
            // Get table structure
            $schema[$tableName] = [
                'columns' => DB::select("DESCRIBE {$tableName}"),
                'indexes' => DB::select("SHOW INDEX FROM {$tableName}")
            ];
        }
        
        return $schema;
    }

    /**
     * Get all table data
     */
    private function getAllData()
    {
        $data = [];
        $tables = ['users', 'harvests', 'sales', 'customers', 'costs', 'prices', 'marketings'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $records = DB::table($table)->get()->toArray();
                $data[$table] = $records;
                $this->line("✓ Backed up {$table}: " . count($records) . " records");
            } else {
                $this->line("⚠ Table {$table} not found, skipping...");
            }
        }
        
        return $data;
    }

    /**
     * Create SQL backup file
     */
    private function createSqlBackup($backup, $backupName)
    {
        $sqlFile = storage_path('backups/' . $backupName . '.sql');
        $sql = "-- KHYSS Farm Database Backup\n";
        $sql .= "-- Created: " . $backup['backup_info']['created_at'] . "\n";
        $sql .= "-- Laravel Version: " . $backup['backup_info']['laravel_version'] . "\n\n";
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        
        foreach ($backup['data'] as $tableName => $records) {
            if (empty($records)) continue;
            
            $sql .= "-- Table: {$tableName}\n";
            $sql .= "TRUNCATE TABLE `{$tableName}`;\n";
            
            if (!empty($records)) {
                $firstRecord = (array) $records[0];
                $columns = array_keys($firstRecord);
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n";
                
                $values = [];
                foreach ($records as $record) {
                    $record = (array) $record;
                    $escapedValues = array_map(function($value) {
                        if ($value === null) return 'NULL';
                        return "'" . addslashes($value) . "'";
                    }, array_values($record));
                    
                    $values[] = '(' . implode(', ', $escapedValues) . ')';
                }
                
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        
        file_put_contents($sqlFile, $sql);
        $this->info("🗄️ SQL backup: " . $sqlFile);
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