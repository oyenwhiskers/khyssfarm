@echo off
echo ================================================
echo    KHYSS Farm Database Backup Utility
echo ================================================
echo.

if "%1"=="" (
    echo Creating automatic backup...
    php artisan db:backup-laravel
) else (
    echo Creating named backup: %1
    php artisan db:backup-laravel --name=%1
)

echo.
echo ================================================
echo Backup completed! Use "backup_list.bat" to view all backups.
echo ================================================
pause