@echo off
echo ================================================
echo    KHYSS Farm Database Restore Utility
echo ================================================
echo.
echo WARNING: This will overwrite your current database!
echo.
set /p confirm="Are you sure you want to continue? (y/N): "

if /i "%confirm%"=="y" (
    echo.
    echo Starting restore process...
    php artisan db:restore-laravel
) else (
    echo.
    echo Restore cancelled.
)

echo.
echo ================================================
pause