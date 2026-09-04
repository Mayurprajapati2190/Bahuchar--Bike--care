@echo off
title Bahuchar Bike Care - Mobile API (LAN)
cd /d "C:\Herd\bahuchar-bike-care"

set "PHP=C:\Users\Suresh Prajapati\.config\herd\bin\php84\php.exe"
if not exist "%PHP%" set "PHP=php"

echo.
echo  Bahuchar Bike Care - LAN API for phone
echo  =====================================
echo  Phone must be on same Wi-Fi as this PC.
echo  API: http://192.168.31.97:8000/api/v1/
echo.
echo  Keep this window open while using the Android app.
echo.

"%PHP%" artisan serve --host=0.0.0.0 --port=8000
pause
