# Free Deployment Guide

## 🚀 Quick Start: Deploy to Render (Recommended)

### Step 1: Prepare Your Code

1. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Ready for deployment"
   git push origin main
   ```

2. **Ensure `.env` is NOT committed** (should be in `.gitignore`)

### Step 2: Deploy on Render

1. **Go to:** https://render.com
2. **Sign up** with GitHub
3. **Create PostgreSQL Database:**
   - Click "New +" → PostgreSQL
   - Name: `library-db`
   - Plan: **Free**
   - Region: Choose closest to you
   - Click "Create Database"
   - **Copy the Internal Database URL** (you'll need it)

4. **Create Web Service:**
   - Click "New +" → Web Service
   - Connect your GitHub repository
   - Select your `library-system` repo
   - Configure:
     - **Name:** `library-system` (or your choice)
     - **Environment:** `PHP`
     - **Region:** Same as database
     - **Branch:** `main` (or your default branch)
     - **Root Directory:** Leave empty
     - **Build Command:**
       ```bash
       composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force
       ```
     - **Start Command:**
       ```bash
       php artisan serve --host=0.0.0.0 --port=$PORT
       ```
     - **Plan:** **Free**

5. **Add Environment Variables:**
   Click "Environment" tab and add:
   ```
   APP_NAME=Library System
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=(will be generated, see below)
   APP_URL=https://your-app-name.onrender.com
   
   DB_CONNECTION=pgsql
   DB_HOST=(from database URL - the hostname)
   DB_PORT=5432
   DB_DATABASE=(from database URL - the database name)
   DB_USERNAME=(from database URL - the username)
   DB_PASSWORD=(from database URL - the password)
   
   SESSION_DRIVER=database
   QUEUE_CONNECTION=database
   
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_FROM_ADDRESS=your-email@gmail.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Generate APP_KEY:**
   - After first deploy, go to "Shell" tab
   - Run: `php artisan key:generate --show`
   - Copy the key
   - Go back to Environment tab
   - Update `APP_KEY` with the generated key
   - Save changes (will trigger redeploy)

7. **Deploy:**
   - Click "Manual Deploy" → "Deploy latest commit"
   - Wait 5-10 minutes for first build

8. **Access your app:**
   - Your app will be at: `https://your-app-name.onrender.com`
   - First load may take 30 seconds (free tier spins down after inactivity)

---

## 🎯 Alternative: Railway (Even Easier)

1. **Go to:** https://railway.app
2. **Sign up** with GitHub
3. **New Project** → **Deploy from GitHub repo**
4. **Add PostgreSQL** database (automatic)
5. **Add environment variables:**
   ```
   APP_ENV=production
   APP_DEBUG=false
   ```
6. **Railway auto-detects Laravel** and sets everything up!
7. **Generate APP_KEY** in the shell/console
8. **Done!** Your app is live

---

## 📝 Important Notes

### Free Tier Limitations:

- **Render:** Spins down after 15 min inactivity (first request takes ~30s)
- **Railway:** $5 credit/month (usually enough for small apps)
- **Fly.io:** 3 VMs, shared resources

### What You Get:

✅ Free SSL certificate  
✅ Free subdomain  
✅ Database included  
✅ Automatic deployments from GitHub  
✅ HTTPS enabled  

### Optional: Custom Domain

Both Render and Railway allow you to add a custom domain for free:
- Render: Settings → Custom Domains
- Railway: Settings → Domains

---

## 🔧 Post-Deployment Setup

1. **Seed database (optional):**
   ```bash
   php artisan db:seed --force
   ```

2. **Set up queue worker (if using):**
   - Render: Add Background Worker service
   - Railway: Add Worker service
   - Command: `php artisan queue:work`

3. **Set up scheduler (for due-date reminders):**
   - Render: Add Cron Job service
   - Railway: Add Cron service
   - Schedule: `* * * * *` (every minute)
   - Command: `php artisan schedule:run`

4. **Chatbot (optional):**
   - Deploy `server.js` as separate service
   - Set `CHATBOT_URL` in main app's environment variables

---

## 🆘 Troubleshooting

**"500 Internal Server Error":**
- Check logs in Render/Railway dashboard
- Ensure `APP_KEY` is set
- Verify database credentials

**"Database connection failed":**
- Check database is running
- Verify `DB_*` environment variables match database URL
- For Render: Use **Internal Database URL** (not External)

**"Page not found":**
- Ensure `APP_URL` matches your actual domain
- Run `php artisan config:cache` in shell

**"Migration failed":**
- Check database exists
- Verify user has CREATE permissions
- Try running migrations manually in shell

---

## 💡 Tips

- **Keep `.env` out of Git** (already in `.gitignore`)
- **Use environment variables** for all secrets
- **Test locally** with production-like settings before deploying
- **Monitor logs** in the dashboard
- **Set up alerts** if available (Railway has this)

---

Need help? Check the platform's documentation or community forums!
