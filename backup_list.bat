@echo off
echo ================================================
echo    KHYSS Farm Database Backup List
echo ================================================
echo.

php artisan db:backups

echo.
echo ================================================
echo Commands:
echo • Create backup: backup_create.bat
echo • Create named backup: backup_create.bat "my_backup_name"
echo • Restore backup: backup_restore.bat
echo ================================================
pause