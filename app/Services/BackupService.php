<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupService
{
    /**
     * Create an automatic backup before risky operations
     */
    public static function createAutoBackup($reason = 'auto')
    {
        try {
            $backupName = "auto_{$reason}_" . Carbon::now()->format('Y-m-d_H-i-s');
            
            Artisan::call('db:backup-laravel', [
                '--name' => $backupName
            ]);
            
            Log::info("Automatic backup created: {$backupName}");
            
            return [
                'success' => true,
                'backup_name' => $backupName,
                'message' => "Automatic backup created successfully"
            ];
            
        } catch (\Exception $e) {
            Log::error("Auto backup failed: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => "Auto backup failed: " . $e->getMessage()
            ];
        }
    }

    /**
     * Clean old backups (keep last 10)
     */
    public static function cleanOldBackups($keepCount = 10)
    {
        try {
            $backupsDir = storage_path('backups');
            
            if (!is_dir($backupsDir)) {
                return ['success' => true, 'message' => 'No backups directory'];
            }

            $jsonBackups = glob($backupsDir . '/*.json');
            $sqlBackups = glob($backupsDir . '/*.sql');
            
            // Sort by modification time (newest first)
            usort($jsonBackups, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            usort($sqlBackups, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            $deletedCount = 0;
            
            // Keep only the newest backups
            $jsonToDelete = array_slice($jsonBackups, $keepCount);
            $sqlToDelete = array_slice($sqlBackups, $keepCount);
            
            foreach ($jsonToDelete as $file) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
            
            foreach ($sqlToDelete as $file) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
            
            return [
                'success' => true,
                'deleted_count' => $deletedCount,
                'message' => "Cleaned {$deletedCount} old backup files"
            ];
            
        } catch (\Exception $e) {
            Log::error("Backup cleanup failed: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => "Cleanup failed: " . $e->getMessage()
            ];
        }
    }

    /**
     * Get backup statistics
     */
    public static function getBackupStats()
    {
        $backupsDir = storage_path('backups');
        
        if (!is_dir($backupsDir)) {
            return [
                'total_backups' => 0,
                'total_size' => 0,
                'latest_backup' => null,
                'oldest_backup' => null
            ];
        }

        $backups = glob($backupsDir . '/*.{json,sql}', GLOB_BRACE);
        
        if (empty($backups)) {
            return [
                'total_backups' => 0,
                'total_size' => 0,
                'latest_backup' => null,
                'oldest_backup' => null
            ];
        }

        $totalSize = array_sum(array_map('filesize', $backups));
        
        $times = array_map('filemtime', $backups);
        
        return [
            'total_backups' => count($backups),
            'total_size' => $totalSize,
            'total_size_formatted' => self::formatBytes($totalSize),
            'latest_backup' => Carbon::createFromTimestamp(max($times)),
            'oldest_backup' => Carbon::createFromTimestamp(min($times))
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}