# cPanel Git Deployment Setup Guide

## Quick Setup (cPanel Git Version Control)

Since your YeahHost cPanel has Git version control, deployment is straightforward!

---

## **Step 1: Set Up Git Repository in cPanel**

1. **Login to cPanel**
2. Go to **Git Version Control** (or **Repository Manager**)
3. Click **Create Repository**
4. Fill in:
   - **Toggle**: Enable **"Clone a Repository"**
   - **Clone URL**: `https://github.com/yourusername/khyss-farm.git`
   - **Repository Path**: `/home/khysscom/` (or your public_html path)
   - **Repository Name**: `khyss-farm` (display name only)
5. Click **Create**

The repository will be cloned to your server automatically!

---

## **Step 2: Add GitHub Secrets**

1. Go to your **GitHub repository**
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Add these 3 secrets:

| Secret | Example Value | Where to Find |
|--------|--------|--------|
| `CPANEL_HOST` | `123.45.67.89` or `yourdomain.com` | YeahHost email or cPanel dashboard |
| `CPANEL_USERNAME` | `khysscom` | Your cPanel username |
| `CPANEL_PASSWORD` | Your cPanel password | Your cPanel login password |
| `CPANEL_PORT` | `22` | Ask YeahHost (usually 22) |
| `DEPLOYMENT_PATH` | `/home/khysscom/` | Same as repository path above |

---

## **Step 3: Set Up .env on Server**

SSH isn't available, so set up manually:

1. **Via cPanel File Manager:**
   - Go to **File Manager** in cPanel
   - Navigate to your app directory
   - Right-click → **Create New File** → Name it `.env`
   - Edit it with your database credentials:

```env
APP_NAME="KHYSS Farm"
APP_ENV=production
APP_KEY=
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password

MAIL_MAILER=log
```

2. **Get your database credentials:**
   - Go to cPanel → **MySQL Databases**
   - Create a new database (if needed)
   - Create database user
   - Note the credentials for `.env`

3. **Generate app key manually:**
   - Via File Manager, right-click public_html
   - Go to **Terminal** (if available in cPanel) or use **cron jobs**
   - Run: `php artisan key:generate`

---

## **Step 4: Set File Permissions**

Via cPanel **File Manager**:

1. Navigate to your app folder
2. Right-click `storage` folder → **Change Permissions**
3. Set to `755` (read/write/execute)
4. Same for `bootstrap/cache`

Or ask YeahHost to run:
```bash
chmod -R 755 storage bootstrap/cache
```

---

## **Step 5: Deploy!**

Now just **push to GitHub**:

```bash
git add .
git commit -m "Ready for deployment"
git push origin main
```

**What happens automatically:**
1. ✅ GitHub Actions runs tests
2. ✅ Connects to your cPanel server (via HTTPS)
3. ✅ Pulls latest code from Git
4. ✅ Installs Composer dependencies
5. ✅ Clears application caches
6. ✅ Runs database migrations

---

## **Monitor Deployment**

1. Go to your GitHub repo
2. Click **Actions** tab
3. Watch workflow run in real-time
4. Check logs if anything fails

---

## **Troubleshooting**

### **Deployment fails with "permission denied"**
- Check `CPANEL_PASSWORD` secret is correct
- Verify `DEPLOYMENT_PATH` matches your Git repo path

### **Git pull fails**
- Check Git is properly set up in cPanel
- Verify repository URL in cPanel matches your GitHub repo

### **Migrations fail**
- Check database credentials in `.env`
- Ensure database user has proper privileges
- Verify database exists in cPanel

### **"composer not found"**
- YeahHost needs to have Composer installed
- Contact support to verify Composer is available

---

## **Manual Deployment (Backup)**

If CI/CD fails, manually pull via cPanel:

1. Go to cPanel → **Git Version Control**
2. Click your repository
3. Click **Pull** to update code manually
4. Run migrations via **Terminal** or **Cron Jobs**

---

## **Useful Links**

- cPanel Git Docs: https://documentation.cpanel.net/display/CPanel/Git+Version+Control
- GitHub Actions: https://github.com/yourusername/khyss-farm/actions
- Your Laravel Logs: Check `storage/logs/laravel.log` via File Manager

---

**Questions?**
- Need help finding cPanel database credentials?
- Having issues with file permissions?
- Contact YeahHost support with your cPanel username!
