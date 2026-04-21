# Swift Steel — cPanel Deployment Guide

**For:** cPanel VPS with CGI/SSH shell access, no sudo privileges.  
PHP extensions managed via cPanel → **Select PHP Version**.

---

## 1. Set the Correct PHP Version & Extensions

1. Log in to cPanel → **Software → Select PHP Version**
2. Select **PHP 8.2** from the dropdown and click **Set as current**
3. In the **PHP Extensions** tab, enable all of these:

| Extension | Reason |
|---|---|
| `pdo_mysql` | Database |
| `mbstring` | String handling |
| `openssl` | Encryption / HTTPS |
| `fileinfo` | File uploads |
| `xml` | XML parsing |
| `curl` | HTTP requests |
| `zip` | Zip extraction |
| `bcmath` | Precision arithmetic |
| `intl` | Internationalisation |
| `gd` or `imagick` | Image processing |
| `exif` | Image uploads |

4. Click **Save**.

---

## 2. Find Your PHP Binary Path

cPanel's PHP binary is **not** `/usr/bin/php`. Find the real path via SSH:

```bash
which php
# → probably /usr/local/bin/php (a wrapper that uses the version selected in cPanel)

# Or check the versioned path directly:
ls /usr/local/php*/bin/php 2>/dev/null || ls /opt/cpanel/ea-php*/root/usr/bin/php 2>/dev/null
# e.g. /opt/cpanel/ea-php82/root/usr/bin/php
```

Test it:
```bash
php -v         # should say PHP 8.2.x
```

If `php` points to the right version, you're fine. Otherwise note the full path — you'll need it in Step 6.

---

## 3. Create the MySQL Database

1. cPanel → **Databases → MySQL Databases**
2. Create a database, e.g. `cpanelusername_swiftsteel`
3. Create a user, e.g. `cpanelusername_ssapp` with a strong password
4. Add the user to the database → grant **ALL PRIVILEGES**

Note down:
- Database name: `cpanelusername_swiftsteel`
- Username: `cpanelusername_ssapp`
- Password: `your-password`
- Host: `localhost`

---

## 4. Install Composer (no sudo required)

```bash
# Download into home directory
cd ~
curl -sS https://getcomposer.org/installer | php

# Move to a local bin directory on your PATH
mkdir -p ~/.local/bin
mv composer.phar ~/.local/bin/composer
chmod +x ~/.local/bin/composer

# Add to PATH (add this to ~/.bashrc too for persistence)
export PATH="$HOME/.local/bin:$PATH"
echo 'export PATH="$HOME/.local/bin:$PATH"' >> ~/.bashrc

# Verify
composer --version
```

---

## 5. SSL Certificate

1. cPanel → **Security → SSL/TLS → AutoSSL**
2. Run AutoSSL — it provisions a free Let's Encrypt certificate for your domain automatically
3. No manual Certbot needed

---

## 6. Configure SSH Alias (local machine)

On your **local development machine** (WSL or Git Bash):

```bash
nano ~/.ssh/config
```

Add:
```
Host swiftsteel
    HostName      your.server.ip.or.cpanel.domain
    User          cpanelusername
    Port          22
    IdentityFile  ~/.ssh/id_ed25519
```

> If port 22 is blocked, try 2222 or check cPanel → Security → SSH Access for the correct port.

Test:
```bash
ssh swiftsteel
```

---

## 7. Upload server-deploy.sh (one-time)

```bash
# From the project root on your local machine:
scp server-deploy.sh swiftsteel:~/
```

> This only needs to be done once. The script lives on the server permanently and handles every future deployment.

---

## 8. First Deployment

The zip now includes `.env.production` — the installer reads it, writes the final `.env`, and saves it to `persistent_storage/` for all future deploys.

```bash
# Build the zip locally
./deploy.sh

# Upload and run on server
# If 'php' already resolves to 8.2 on your server:
ssh swiftsteel 'bash ~/server-deploy.sh ~/deploy_YYYYMMDD_HHMMSS.zip'

# If you need to specify the PHP binary explicitly:
ssh swiftsteel 'PHP_BIN=/opt/cpanel/ea-php82/root/usr/bin/php bash ~/server-deploy.sh ~/deploy_YYYYMMDD_HHMMSS.zip'
```

Or build and deploy in one command:
```bash
./deploy.sh --deploy
```

When the script finishes it will print:
```
  Live from : /home/cpanelusername/deploy_YYYYMMDD_HHMMSS
  Uploads   : /home/cpanelusername/persistent_storage/app/public
  Backup    : /home/cpanelusername/public_backups/deploy_YYYYMMDD_HHMMSS

  ➡  NEXT STEP: Open the installer in your browser:
     https://yourdomain.com/install.php
```

Complete the 4-step web installer:
1. **Requirements** — all green (you already enabled extensions in Step 1)
2. **Database** — enter the credentials from Step 3, plus App URL and timezone
3. **Mail** — configure SMTP (see Step 9) or leave as `log` for now
4. **Admin Account** — create the first admin user

The installer writes `.env`, generates an app key, runs migrations, creates your admin account, caches config/routes/views, then **self-deletes**. The site is live.

---

## 9. Configure SMTP (cPanel Mail)

cPanel provides an outgoing mail server. Use these settings in the installer (Step 3 → Mail):

| Field | Value |
|---|---|
| Driver | `smtp` |
| Host | `mail.yourdomain.com` |
| Port | `587` |
| Encryption | `tls` |
| Username | `noreply@yourdomain.com` (a cPanel email account) |
| Password | password for that email account |
| From Address | `noreply@yourdomain.com` |
| From Name | Swift Steel |

To create the email account: cPanel → **Email → Email Accounts → Create**.

---

## 10. Subsequent Deployments

```bash
./deploy.sh --deploy
```

That's it. The script will:
- Extract the zip to a new timestamped folder
- Back up the current `public_html` contents to `~/public_backups/deploy_YYYYMMDD_HHMMSS/`
- Restore `.env` from `persistent_storage/`
- Run `migrate --force`, recache config/routes/views
- Update `public_html/index.php` to point to the new folder
- Update the `~/current` symlink

Or manually if you need to specify the PHP binary:
```bash
./deploy.sh
scp deployment/deploy_*.zip swiftsteel:~/
ssh swiftsteel 'PHP_BIN=/opt/cpanel/ea-php82/root/usr/bin/php bash ~/server-deploy.sh ~/deploy_YYYYMMDD_HHMMSS.zip'
```

---

## 11. Cron Job (Laravel Scheduler)

`server-deploy.sh` automatically maintains a `~/current` symlink pointing to the active deploy folder — you never need to update the cron command after a deployment.

1. cPanel → **Advanced → Cron Jobs**
2. Set **Common Settings** to `* * * * *` (every minute)
3. Command:

```bash
/usr/local/bin/php /home/cpanelusername/current/artisan schedule:run >> /dev/null 2>&1
```

Set this once and forget it.

---

## 12. Directory Structure After Deployment

```
/home/cpanelusername/
├── public_html/                         ← Apache web root (cPanel manages this)
│   ├── index.php                        ← Points to active deploy folder (auto-updated)
│   ├── storage -> ~/persistent_storage/app/public   ← Symlink (auto-created)
│   ├── build/                           ← Compiled frontend assets
│   └── assets/
├── persistent_storage/
│   ├── .env.production                  ← Saved by installer; restored on each deploy
│   ├── installed                        ← Lockfile (prevents re-running installer)
│   └── app/public/                      ← ALL uploaded files (images, etc.) — never deleted
├── public_backups/
│   └── deploy_20260419_120000/          ← Snapshot of public_html before it was replaced
├── current -> deploy_20260419_193636/   ← Stable symlink for cron (auto-updated)
├── deploy_20260419_120000/              ← Previous deploy (safe to delete after verification)
└── deploy_20260419_193636/              ← Active deploy
```

---

## 13. Rollback

```bash
# Restore previous public_html assets from the backup
cp -r ~/public_backups/deploy_20260419_193636/. ~/public_html/

# Point index.php back at the previous deploy folder
sed -i 's|deploy_20260419_193636|deploy_20260419_120000|g' ~/public_html/index.php

# Update the current symlink
ln -sfn ~/deploy_20260419_120000 ~/current

# Roll back database if needed
cd ~/deploy_20260419_120000
php artisan migrate:rollback
```

> The backup folder name matches the deploy that **replaced** those files, making it easy to identify which snapshot to restore.

---

## 14. Monitoring & Logs

```bash
# Laravel log
tail -f ~/deploy_YYYYMMDD_HHMMSS/storage/logs/laravel.log

# Check PHP-FPM / Apache error log (cPanel path varies)
tail -f ~/logs/yourdomain.com.error.log
# or
tail -f /usr/local/apache/logs/error_log
```

---

## 15. Security Checklist

- [ ] `APP_DEBUG=false` in `.env.production`
- [ ] `APP_ENV=production` in `.env.production`
- [ ] MySQL user only has permissions on the app database
- [ ] SSH password authentication disabled in cPanel → Security → SSH Access (use keys only)
- [ ] AutoSSL is active and certificate is valid
- [ ] `install.php` is auto-deleted after installation completes
- [ ] `persistent_storage/` is **outside** `public_html` — not web accessible
- [ ] `.env.production` is stored in `persistent_storage/` — outside `public_html`
- [ ] Old deploy folders cleaned up periodically (keep last 2 for rollback safety)
