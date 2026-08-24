@echo off
title Bahuchar Bike Care - Web App
cd /d "%~dp0"

echo.
echo  Bahuchar Bike Care - Starting Web App
echo  =====================================
echo.

where php >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP not found. Install Laravel Herd first.
    pause
    exit /b 1
)

echo [1/5] Starting Laravel Herd if needed...
tasklist /FI "IMAGENAME eq Herd.exe" | find /I "Herd.exe" >nul
if errorlevel 1 (
    if exist "%ProgramFiles%\Herd\Herd.exe" (
        start "" "%ProgramFiles%\Herd\Herd.exe"
        timeout /t 8 /nobreak >nul
    ) else (
        echo ERROR: Laravel Herd is not installed or not running.
        echo Open Herd, then run this script again.
        pause
        exit /b 1
    )
)

echo [2/5] Clearing Laravel cache...
php artisan optimize:clear >nul 2>&1

echo [3/5] Building frontend assets...
call npm run build
if errorlevel 1 (
    echo WARNING: npm build failed. Using existing assets if available.
)

echo [4/5] Checking health...
curl -s -o nul -w "HTTP %%{http_code}" http://bahuchar-bike-care.test/up
echo.

echo [5/5] Opening browser...
start "" "http://bahuchar-bike-care.test/login"

echo.
echo  App URL:  http://bahuchar-bike-care.test
echo  Login:    http://bahuchar-bike-care.test/login
echo  Dashboard: http://bahuchar-bike-care.test/dashboard
echo.
echo  Staff login:
echo    Email:    mayurprajapati2190@gmail.com
echo    Password: Mayur@2190
echo.
echo  Make sure Laravel Herd is running.
echo.
pause
