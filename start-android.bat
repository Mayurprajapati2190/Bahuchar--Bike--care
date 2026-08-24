@echo off
title Bahuchar Bike Care - Android Staff App
cd /d "%~dp0mobile\android"

echo.
echo  Bahuchar Bike Care - Android Setup
echo  ==================================
echo.

if not exist "local.properties" (
    echo Creating local.properties from example...
    copy /Y local.properties.example local.properties >nul
    echo Edit local.properties if your SDK path differs.
)

where studio64.exe >nul 2>&1
if errorlevel 1 (
    if exist "%ProgramFiles%\Android\Android Studio\bin\studio64.exe" (
        set "STUDIO=%ProgramFiles%\Android\Android Studio\bin\studio64.exe"
    ) else (
        echo Open Android Studio manually and select:
        echo   %cd%
        goto :done
    )
) else (
    set "STUDIO=studio64.exe"
)

tasklist /FI "IMAGENAME eq Herd.exe" | find /I "Herd.exe" >nul
if errorlevel 1 (
    if exist "%ProgramFiles%\Herd\Herd.exe" (
        echo Starting Laravel Herd for the API...
        start "" "%ProgramFiles%\Herd\Herd.exe"
    )
)

echo Opening Android Studio...
start "" "%STUDIO%" "%cd%"

:done
echo.
echo  Project path: %cd%
echo  Run config:   staff  (shop login app)
echo                customer (OTP app for customers)
echo.
echo  Backend must be running: http://bahuchar-bike-care.test
echo  Staff login: mayurprajapati2190@gmail.com / Mayur@2190
echo.
pause
