# Bahuchar Bike Care — Staff Android App

Native Kotlin + Jetpack Compose app for shop staff.

## Open in Android Studio

**Option 1:** Double-click `c:\Herd\bahuchar-bike-care\start-android.bat`

**Option 2:** Android Studio → **File → Open** → select this folder

Wait for Gradle sync, then choose run configuration **staff** and run on emulator.

## Login

Same as web dashboard:
- Email: `mayurprajapati2190@gmail.com`
- Password: `Mayur@2190`

## Backend

Laravel Herd must be running: `http://bahuchar-bike-care.test`

API config is in `local.properties`:
```properties
api.base.url=http://10.0.2.2/api/v1/
api.host.header=bahuchar-bike-care.test
```

**Physical phone:** change `api.base.url` to your PC LAN IP (same Wi‑Fi).

## Features

- Staff login
- Dashboard
- Customers (list, detail, **create new**)
- Services (list, detail, **create new**, complete)
- Bills (list, detail, mark paid)

## Project modules

| Module | App ID |
|--------|--------|
| `staff` | com.bahuchar.bikecare.staff |
| `customer` | com.bahuchar.bikecare.customer (OTP login for customers) |
| `core` | shared API + UI |
