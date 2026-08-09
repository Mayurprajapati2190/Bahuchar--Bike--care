# Bahuchar Bike Care — Android Apps

Native Android apps for shop staff and customers, connected to the Laravel REST API.

## Project structure

```
mobile/android/
├── core/       Shared API client, models, theme
├── staff/      Staff app (com.bahuchar.bikecare.staff)
└── customer/   Customer app (com.bahuchar.bikecare.customer)
```

## Requirements

- Android Studio Ladybug (2024.2+) or newer
- JDK 17
- Android SDK 35
- Laravel backend running (local Herd or production HTTPS)

## Open in Android Studio

1. Open **File → Open** and select `mobile/android/`
2. Let Gradle sync complete (downloads wrapper on first open)
3. Select run configuration: **staff** or **customer**
4. Run on emulator or physical device

## API base URL

Each app module defines `API_BASE_URL` in `build.gradle.kts`:

| Build | Default | Use when |
|-------|---------|----------|
| Debug | `http://10.0.2.2/api/v1/` | Android emulator → host machine |
| Release | `https://yourdomain.com/api/v1/` | Production (HTTPS required) |

### Local development tips

- **Emulator:** `10.0.2.2` maps to your PC's `localhost`. If Herd vhost doesn't respond, use your PC's LAN IP instead, e.g. `http://192.168.1.5/api/v1/`
- **Physical device:** Use LAN IP; phone and PC must be on the same Wi‑Fi
- **Cleartext HTTP:** Enabled only in debug builds (`usesCleartextTraffic="true"`)

Change debug URL in:
- `staff/build.gradle.kts` → `buildConfigField("String", "API_BASE_URL", ...)`
- `customer/build.gradle.kts` → same

## Staff app features

- Email/password login (same credentials as web dashboard)
- Dashboard stats and quick lists
- Customers list and detail
- Services list, detail, complete service
- Bills list, detail, mark payment

## Customer app features

- OTP login (phone must exist in shop database)
- Next service due date
- Shop contact info
- My bikes, service history, bills

**OTP in dev:** When `MSG91_ENABLED=false`, OTP codes are written to Laravel logs (`storage/logs/laravel.log`).

## Production release checklist

### Backend

1. Deploy Laravel with HTTPS ([deploy/DEPLOYMENT.md](../../deploy/DEPLOYMENT.md))
2. Ensure API routes work: `GET https://yourdomain.com/api/v1/dashboard` (with auth token)
3. Set `CORS_ALLOWED_ORIGINS=*` or restrict to your domain
4. Queue worker running for SMS jobs

### Android signing

1. **Build → Generate Signed App Bundle** in Android Studio
2. Create upload keystore (keep backup safe — required for all future updates)
3. Build **Android App Bundle (.aab)** for Play Store

```bash
cd mobile/android
./gradlew :staff:bundleRelease
./gradlew :customer:bundleRelease
```

Output: `staff/build/outputs/bundle/release/` and `customer/build/outputs/bundle/release/`

### Play Store

1. Create two apps: **Bahuchar Staff** (internal testing first) and **Bahuchar Bike Care** (customer)
2. Upload `.aab` to Internal testing track
3. Add testers by email
4. Set `API_BASE_URL` in release `build.gradle.kts` to your HTTPS domain before building

### Security

- Staff tokens stored in DataStore (app-private)
- 401 responses should clear token and return to login (handled via auth interceptor)
- Customer OTP endpoints rate-limited (5/min request, 10/min verify)

## Branding

Apps use Bahuchar colors: slate `#0F172A`, amber `#F59E0B`. Adaptive icons are in each module's `res/drawable/` and `res/mipmap-anydpi-v26/`.
