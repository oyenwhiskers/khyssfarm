# CI/CD Deployment Guide for cPanel (YeahHost)

## Overview
This guide explains how to set up automated CI/CD deployment for your Laravel KHYSS Farm system on cPanel using GitHub Actions.

## Prerequisites

1. **SSH Access on cPanel**: Your YeahHost account must have SSH access enabled
2. **Git Installation**: Git must be installed on the cPanel server
3. **GitHub Repository**: Code pushed to GitHub (public or private)
4. **Composer**: Available on the server

## Step-by-Step Setup

### Step 1: Enable SSH Access on cPanel

1. Log in to your YeahHost cPanel
2. Navigate to **SSH Access**
3. Click **Manage SSH Keys**
4. Generate a new key pair (or use existing one)
5. Save the private key securely on your computer

### Step 2: Create GitHub Repository

If you haven't already:

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/yourusername/khyss-farm.git
git push -u origin main
```

### Step 3: Set Up SSH Key in cPanel

1. In cPanel SSH Access → Manage SSH Keys
2. Import your public key (the system will generate one if needed)
3. Authorize the key
4. Test SSH connection:
   ```bash
   ssh username@cpanel-host -p port
   ```

### Step 4: Configure GitHub Secrets

1. Go to your GitHub repository
2. Navigate to **Settings** → **Secrets and variables** → **Actions**
3. Add these secrets:

| Secret Name | Value | Example |
|------------|-------|---------|
| `CPANEL_HOST` | Your cPanel server IP/hostname | `123.45.67.89` or `yourdomain.com` |
| `CPANEL_USERNAME` | Your cPanel username | `cPanel_user` |
| `SSH_PRIVATE_KEY` | Your SSH private key content | (paste entire private key) |
| `SSH_PORT` | SSH port (usually 22, check with host) | `22` or `2222` |
| `DEPLOYMENT_PATH` | Path to your Laravel app | `/home/username/public_html/khyss-farm` |

**How to get SSH Private Key content:**
- Open your private key file (id_rsa) in a text editor
- Copy the entire content (including `-----BEGIN PRIVATE KEY-----` and `-----END PRIVATE KEY-----`)
- Paste into the GitHub Secret

### Step 5: Set Up Git on cPanel Server

1. SSH into your cPanel server:
   ```bash
   ssh username@your-cpanel-host -p port
   ```

2. Navigate to your Laravel app directory:
   ```bash
   cd /home/username/public_html/khyss-farm
   ```

3. Clone your repository:
   ```bash
   git clone https://github.com/yourusername/khyss-farm.git .
   ```

4. Configure Git user (required for deployments):
   ```bash
   git config user.email "your-email@example.com"
   git config user.name "Your Name"
   ```

### Step 6: Set File Permissions

On cPanel, run these commands:

```bash
# Make storage and cache directories writable
chmod -R 775 storage bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache

# Set proper permissions for public files
chmod -R 755 public
```

### Step 7: Create .env File on cPanel

1. SSH into your server
2. Copy the example file:
   ```bash
   cp .env.example .env
   ```

3. Edit .env with your cPanel database credentials:
   ```bash
   nano .env
   ```

4. Update these values:
   ```
   APP_URL=https://yourdomain.com
   DB_HOST=localhost
   DB_DATABASE=cpanel_username_dbname
   DB_USERNAME=cpanel_username_dbuser
   DB_PASSWORD=your_db_password
   ```

5. Generate app key:
   ```bash
   php artisan key:generate
   ```

### Step 8: Deploy!

Simply push your code to GitHub:

```bash
git add .
git commit -m "Your commit message"
git push origin main
```

GitHub Actions will:
1. ✅ Run all tests
2. ✅ Connect to your cPanel server via SSH
3. ✅ Pull latest code
4. ✅ Install dependencies
5. ✅ Run migrations
6. ✅ Clear caches
7. ✅ Set permissions

## Monitoring Deployments

1. Go to your GitHub repository
2. Click **Actions** tab
3. Watch the workflow run in real-time
4. Check logs if deployment fails

## Troubleshooting

### SSH Connection Fails
- Check `CPANEL_HOST`, `CPANEL_USERNAME`, `SSH_PRIVATE_KEY`, `SSH_PORT` in GitHub Secrets
- Verify SSH is enabled in cPanel
- Test manual SSH connection first

### Permission Denied on Storage
```bash
# SSH into server and run:
chmod -R 775 storage bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache
```

### Composer Install Fails
- Check PHP version matches your local environment
- Ensure sufficient disk space on server
- Check `composer.json` lock file is up to date

### Database Migration Fails
- Verify database credentials in `.env`
- Ensure database user has proper privileges
- Check database exists and is accessible

### Git Pull Fails
- Verify Git configuration on server: `git config user.email` and `git config user.name`
- Check repository is properly cloned with correct remote URL
- Ensure GitHub token/key access works (for private repos)

## Manual Deployment (Backup Method)

If CI/CD fails, you can deploy manually via SSH:

```bash
ssh username@cpanel-host -p port

cd /path/to/app

git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache
```

## Advanced Configuration

### Set Up Database Backup Before Deployment

Add this to deploy.yml before `php artisan migrate`:

```yaml
- name: Backup Database
  script: |
    mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < backup_$(date +%Y%m%d_%H%M%S).sql
```

### Enable Email Notifications

Add to your GitHub Actions workflow to notify on failures:

```yaml
- name: Notify Deployment Status
  if: always()
  uses: appleboy/telegram-action@master
  with:
    to: ${{ secrets.TELEGRAM_CHAT_ID }}
    token: ${{ secrets.TELEGRAM_TOKEN }}
    message: Deployment ${{ job.status }}
```

### Schedule Automated Backups

```yaml
on:
  schedule:
    - cron: '0 2 * * *'  # Daily at 2 AM
```

## Useful Commands

```bash
# Check GitHub Actions logs
# Go to Actions tab in GitHub

# Test workflow locally
brew install act
act -l

# View deployment status
git log --oneline -10

# Rollback to previous version
git revert <commit-hash>
git push origin main
```

## Security Best Practices

1. ✅ Never commit `.env` file
2. ✅ Use strong SSH keys (4096-bit RSA minimum)
3. ✅ Rotate GitHub Secrets regularly
4. ✅ Use read-only database user for backups
5. ✅ Enable 2FA on GitHub account
6. ✅ Keep SSH keys private and secure
7. ✅ Use HTTPS for all connections
8. ✅ Monitor failed deployment attempts in GitHub Actions

## Support

If you encounter issues:
1. Check GitHub Actions logs for detailed error messages
2. SSH into server and check error logs: `tail -f storage/logs/laravel.log`
3. Contact YeahHost support for cPanel/server-level issues
4. Verify all GitHub Secrets are correctly set

---

**Next Steps:**
- Set up GitHub repository
- Configure GitHub Secrets with your cPanel details
- Make a test commit and watch the deployment happen!
