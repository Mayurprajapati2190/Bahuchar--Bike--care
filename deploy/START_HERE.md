# Start here — Deploy Bahuchar Bike Care

## Wrong hosts (will fail)

| Host | Why it fails |
|------|----------------|
| Netlify | Static only — no PHP/MySQL |
| Vercel | Node/static — no Laravel server |

Disconnect those sites. Do not keep redeploying this repo there.

## Correct hosts (pick one)

| Your hosting | Open this guide |
|--------------|-----------------|
| Hostinger VPS | [HOSTINGER.md](HOSTINGER.md) |
| DigitalOcean Droplet | [DIGITALOCEAN.md](DIGITALOCEAN.md) |
| cPanel shared hosting | [CPANEL.md](CPANEL.md) |
| Any Ubuntu VPS (full detail) | [DEPLOYMENT.md](DEPLOYMENT.md) |

## Fastest path (recommended)

1. Buy a small **Ubuntu VPS** (Hostinger / DigitalOcean / Contabo)  
2. Point domain **A record** to server IP  
3. SSH in and follow **HOSTINGER.md** or **DIGITALOCEAN.md**  
4. Open `https://yourdomain.com/up` — should say OK  
5. Login with the admin user from `AdminUserSeeder`

## Helper files

| File | Use |
|------|-----|
| `bootstrap-server.sh` | Install PHP, Nginx, MySQL, Node on Ubuntu |
| `deploy.sh` | Update app after first install |
| `.env.production.example` | Production env template |
| `nginx.conf` | Nginx site config |
| `supervisor-worker.conf` | Background queue worker |
