@echo off
title Bahuchar Bike Care - Mobile API (LAN)
cd /d "C:\Herd\bahuchar-bike-care"

set "PHP=C:\Users\Suresh Prajapati\.config\herd\bin\php84\php.exe"
if not exist "%PHP%" set "PHP=php"

echo.
echo  Bahuchar Bike Care - LAN API for Android phone
echo  ==============================================
echo.
echo  1. Keep THIS window open while using the phone app.
echo  2. Phone and PC must be on the SAME Wi-Fi.
echo  3. On phone browser test: http://192.168.31.97:8000/up
echo  4. Then open the Bahuchar app and log in.
echo.
echo  API: http://192.168.31.97:8000/api/v1/
echo.

"%PHP%" artisan serve --host=0.0.0.0 --port=8000
echo.
echo  API stopped.
pause
