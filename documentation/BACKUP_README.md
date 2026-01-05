# KHYSS Farm Database Backup System

## Overview
This backup system protects your valuable chili farm data from accidental loss. It creates both SQL and JSON backups that can be easily restored.

## Quick Start (Using Batch Files)

### Windows Users - Easy Method:
- **Create Backup**: Double-click `backup_create.bat`
- **List Backups**: Double-click `backup_list.bat` 
- **Restore Backup**: Double-click `backup_restore.bat`

### Named Backups:
To create a backup with a specific name, open Command Prompt in the project folder and run:
```
backup_create.bat "before_major_update"
```

## Command Line Usage

### Create Backups
```bash
# Create automatic backup
php artisan db:backup-laravel

# Create named backup
php artisan db:backup-laravel --name=before_update

# Create backup before risky operation
php artisan db:backup-laravel --name=before_migration
```

### List Backups
```bash
php artisan db:backups
```

### Restore Backups
```bash
# Interactive restore (shows list to choose from)
php artisan db:restore-laravel

# Restore specific backup
php artisan db:restore-laravel backup_name_without_extension
```

## Backup Types

### 1. JSON Backups (.json)
- **Purpose**: Easy to read, portable, version-controllable
- **Use**: Data inspection, cross-platform compatibility
- **Contains**: All table data + metadata

### 2. SQL Backups (.sql) 
- **Purpose**: Fast database restoration
- **Use**: Quick recovery, database migration
- **Contains**: SQL INSERT statements

## Backup Locations
All backups are stored in: `storage/backups/`

## What's Backed Up
- ✅ Harvest records
- ✅ Sales data
- ✅ Customer information
- ✅ Cost tracking
- ✅ Pricing data
- ✅ Marketing campaigns
- ✅ User accounts

## Best Practices

### 1. Regular Backups
- Create backups before major updates
- Weekly backups for active farms
- Before importing large datasets

### 2. Naming Convention
- `before_update_YYYY-MM-DD`
- `weekly_backup_YYYY-MM-DD`
- `before_migration_YYYY-MM-DD`

### 3. Storage Management
- Keep last 10 backups automatically
- Archive important backups separately
- Store backups in multiple locations

## Emergency Recovery

### If Database is Lost:
1. Run `backup_list.bat` to see available backups
2. Run `backup_restore.bat` 
3. Select the most recent backup
4. Confirm restoration

### If Laravel is Broken:
1. Restore from SQL backup using MySQL directly:
   ```sql
   mysql -u username -p database_name < backup_file.sql
   ```

## Backup Service Integration

The `BackupService` class provides:
- Automatic backups before risky operations
- Backup cleanup (keeps last 10)
- Backup statistics and monitoring

### Using in Code:
```php
use App\Services\BackupService;

// Create auto backup before dangerous operation
$result = BackupService::createAutoBackup('before_migration');

// Clean old backups
BackupService::cleanOldBackups(10);

// Get backup statistics
$stats = BackupService::getBackupStats();
```

## Troubleshooting

### "No mysqldump found"
- Use Laravel commands instead: `php artisan db:backup-laravel`
- JSON backups work without external dependencies

### "Permission denied"
- Check `storage/backups/` folder permissions
- Run command prompt as administrator

### "Backup failed"
- Check database connection
- Verify disk space
- Check Laravel logs in `storage/logs/`

## Security Notes
- Backups contain sensitive data
- Store in secure locations
- Don't commit backups to public repositories
- Consider encryption for production

## Support
For issues with the backup system, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Database connection settings in `.env`
3. Disk space availability
4. File permissions on `storage/` folder

---
**Remember**: Regular backups save farms! 🌶️💾