# 🚀 SmartWatch Hub - Deployment & Git Guide

Complete instructions for deploying your application to GitHub and cloud platforms.

---

## 📋 Table of Contents

1. [Git Setup](#-git-setup)
2. [GitHub Deployment](#-github-deployment)
3. [Local Development](#-local-development)
4. [Render.com Deployment](#rendercom-deployment)
5. [Azure Deployment](#-azure-deployment)
6. [Docker](#-docker)
7. [Environment Variables](#-environment-variables)
8. [Troubleshooting](#-troubleshooting)

---

## 🔧 Git Setup

### Initial Git Configuration (First Time Only)

```powershell
# Open PowerShell and configure Git globally
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Verify configuration
git config --global --list
```

### Check Current Repository Status

```powershell
cd c:\xampp\htdocs\SMARTWATCHES

# View current status
git status

# View commit history
git log --oneline -10

# View remote configuration
git remote -v
```

---

## 📤 GitHub Deployment

### Step 1: Create GitHub Repository

1. Go to [GitHub.com](https://github.com)
2. Sign in to your account
3. Click **"+"** icon (top right) → **"New repository"**
4. Fill in details:
   - **Repository name**: `smartwatch-hub` (or your preferred name)
   - **Description**: "Full-featured e-commerce platform for smartwatches"
   - **Visibility**: Choose **Public** (visible to all) or **Private** (only you)
   - **Initialize repository**: Leave unchecked (we have files already)
5. Click **"Create repository"**

### Step 2: Connect Local Repository to GitHub

After creating the repository, GitHub shows you commands to run. Open PowerShell:

```powershell
cd c:\xampp\htdocs\SMARTWATCHES

# Add remote origin (replace YOUR_USERNAME and repo name)
git remote add origin https://github.com/YOUR_USERNAME/smartwatch-hub.git

# Verify remote was added
git remote -v

# Expected output:
# origin  https://github.com/YOUR_USERNAME/smartwatch-hub.git (fetch)
# origin  https://github.com/YOUR_USERNAME/smartwatch-hub.git (push)
```

### Step 3: Prepare for Push

```powershell
# Add all files to staging
git add .

# Check what will be committed
git status

# Important: Verify .env is NOT listed (should be in .gitignore)
# You should see .env.example instead
```

### Step 4: First Commit (if not already committed)

```powershell
# Check if there are uncommitted changes
git status

# If there are changes, commit them
git commit -m "Initial commit: Production-ready e-commerce platform

- CRUD operations for products
- Contact form with validation
- Responsive design and accessibility
- Database schema with auto-creation
- Complete admin panel"
```

### Step 5: Push to GitHub

```powershell
# Push to GitHub (first time)
git push -u origin master

# Or if your branch is 'main':
git push -u origin main

# Expected output:
# Counting objects: XX, done.
# Writing objects: 100% (XX/XX)...
# ...
# remote: GitHub has been set up...
```

### Step 6: Verify on GitHub

1. Refresh your GitHub repository page
2. You should see all project files
3. ✅ Verify `.env.example` is present
4. ✅ Verify `.env` is NOT present
5. ✅ Verify all PHP files are there

### Troubleshooting GitHub Push

**Error: "fatal: 'origin' does not appear to be a git repository"**
```powershell
# You're not in the correct directory
cd c:\xampp\htdocs\SMARTWATCHES
git status
```

**Error: "fatal: The current branch master has no upstream branch"**
```powershell
# First time pushing - use -u flag
git push -u origin master
```

**Error: "Permission denied (publickey)"**
```powershell
# You need SSH keys or use HTTPS with token
# Try HTTPS URL instead:
git remote set-url origin https://github.com/USERNAME/smartwatch-hub.git
```

---

## 💻 Local Development

### Regular Git Workflow

**Making Changes:**
```powershell
cd c:\xampp\htdocs\SMARTWATCHES

# Check status
git status

# See what changed
git diff

# Stage specific changes
git add admin.php functions.php

# Or stage everything
git add .

# Commit with descriptive message
git commit -m "Add new feature description"

# Push to GitHub
git push origin main
```

**Pulling Latest Changes:**
```powershell
# If working on team or multiple machines
git pull origin main
```

**Creating a New Feature Branch:**
```powershell
# Create and switch to new branch
git checkout -b feature/add-search

# Make changes and commit
git add .
git commit -m "Add product search functionality"

# Push branch
git push origin feature/add-search

# Create Pull Request on GitHub
# Then merge to main
```

---

## 🎯 Render.com Deployment

### Prerequisites
- GitHub account (repo pushed)
- Render account (free at [render.com](https://render.com))

### Deployment Steps

1. **Create Render Account**
   - Go to [render.com](https://render.com)
   - Sign up with GitHub
   - Authorize access to your repositories

2. **Create New Web Service**
   - Click **"New +"** → **"Web Service"**
   - Connect your GitHub account
   - Select `smartwatch-hub` repository
   - Configure:
     - **Name**: `smartwatch-hub`
     - **Environment**: `PHP`
     - **Build Command**: `composer install 2>/dev/null || true`
     - **Start Command**: `php -S localhost:8080`

3. **Configure Environment**
   - Go to **Environment** tab
   - Add variables:
     ```
     DB_HOST=<mysql-host>
     DB_NAME=smartwatch_db
     DB_USER=<mysql-user>
     DB_PASS=<mysql-password>
     SITE_URL=https://yourdomain.onrender.com
     ENVIRONMENT=production
     DEBUG=false
     ```

4. **Setup Database** (on Render or external)
   - Option A: Use Render MySQL (if available)
   - Option B: Use external MySQL (e.g., Planetscale, AWS RDS)
   - Option C: Use SQLite (easier for testing)

5. **Deploy**
   - Click **"Deploy"**
   - Monitor build logs
   - Wait for "live" status
   - Access your site: `https://smartwatch-hub.onrender.com`

---

## ☁️ Azure Deployment

### Prerequisites
- GitHub repository pushed
- Azure account (free tier available)

### Deployment Steps

1. **Create Azure Account**
   - Go to [azure.microsoft.com](https://azure.microsoft.com)
   - Click "Start free"
   - Sign up with Microsoft account

2. **Create Resource Group**
   - Search "Resource groups"
   - Click "Create"
   - Enter name and region

3. **Create App Service**
   - Search "App Services"
   - Click "Create"
   - Configure:
     - **Runtime stack**: PHP 8.0+
     - **Operating system**: Linux
     - **Region**: Choose your region
     - **App Service Plan**: Free tier (B1)

4. **Configure Deployment**
   - Go to **Deployment Center**
   - **Source**: GitHub
   - **Organization**: Your GitHub account
   - **Repository**: smartwatch-hub
   - **Branch**: main
   - Save and wait for build

5. **Setup Database**
   - Create **Azure Database for MySQL**
   - Configure firewall rules
   - Note connection details

6. **Set Environment Variables**
   - Go to **Configuration** → **Application settings**
   - Add variables:
     ```
     DB_HOST = <azure-mysql-host>
     DB_NAME = smartwatch_db
     DB_USER = <username>
     DB_PASS = <password>
     SITE_URL = https://yourapp.azurewebsites.net
     ENVIRONMENT = production
     DEBUG = false
     ```

7. **Redeploy**
   - Go to **Deployment Center**
   - Click **Sync** to redeploy
   - Wait for "Success" status

---

## 🐳 Docker Deployment

### Local Docker Development

**Create `Dockerfile`:**
```dockerfile
FROM php:8.1-apache

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
```

**Create `docker-compose.yml`:**
```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    environment:
      DB_HOST: mysql
      DB_NAME: smartwatch_db
      DB_USER: root
      DB_PASS: root_password
    depends_on:
      - mysql
    volumes:
      - .:/var/www/html

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: smartwatch_db
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

**Run Docker:**
```powershell
# Build and start containers
docker-compose up -d

# Access application
# http://localhost:8080

# View logs
docker-compose logs -f web

# Stop containers
docker-compose down
```

---

## 🔐 Environment Variables

### Development (local .env)
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=smartwatch_db
DB_USER=root
DB_PASS=
SITE_URL=http://localhost/SMARTWATCHES/
ENVIRONMENT=development
DEBUG=true
```

### Production (cloud .env)
```env
DB_HOST=prod-database-server.com
DB_PORT=3306
DB_NAME=smartwatch_db
DB_USER=smartwatch_user
DB_PASS=STRONG_PASSWORD_HERE
SITE_URL=https://yourdomain.com
ENVIRONMENT=production
DEBUG=false
SESSION_LIFETIME=7200
```

### Important Security Notes
- ✅ Never commit `.env` file
- ✅ Use `.env.example` as template
- ✅ Store production passwords securely
- ✅ Use strong passwords (20+ characters)
- ✅ Rotate passwords regularly
- ✅ Use separate credentials for each environment

---

## 📊 Monitoring & Logs

### Local Logs
```powershell
# Apache error log
C:\xampp\apache\logs\error.log

# MySQL log
C:\xampp\mysql\data\*.err

# PHP errors (check application)
http://localhost/SMARTWATCHES/  # Check displayed errors
```

### Cloud Logs
- **Render**: Deployment tab → Logs
- **Azure**: Application Insights or Log Stream
- **Docker**: `docker-compose logs -f`

---

## 🔄 Continuous Updates

### Push Updates to Production

```powershell
# Make changes locally
# Test thoroughly
git add .
git commit -m "Fix: Descriptive message about changes"
git push origin main

# Cloud platform auto-redeploys (if CI/CD configured)
```

### Rollback if Needed
```powershell
# View commit history
git log --oneline

# Revert to previous commit
git revert <commit-id>
git push origin main
```

---

## ✅ Pre-Deployment Checklist

- [ ] All files committed to Git
- [ ] `.env` NOT in repository
- [ ] `.env.example` IS in repository
- [ ] Database schema auto-creates
- [ ] All tests pass locally
- [ ] Environment variables documented
- [ ] README.md is up to date
- [ ] Security settings configured
- [ ] HTTPS enabled on production
- [ ] Database backups configured
- [ ] Error logging configured
- [ ] Admin credentials secured

---

## 🆘 Troubleshooting

**Database Connection Failed**
- Verify credentials in `.env`
- Check MySQL is running (local) or accessible (cloud)
- Verify firewall allows connection
- Check database host is correct

**Files Not Updating**
- Clear browser cache (Ctrl+F5)
- Verify files were pushed to Git
- Check cloud platform shows latest commit

**500 Internal Server Error**
- Check PHP error logs
- Verify database exists
- Check file permissions
- Review application logs

**Form Not Working**
- Verify database tables created
- Check database connection
- Review server error logs

---

## 📞 Support Resources

- [PHP Documentation](https://www.php.net/manual/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Git Documentation](https://git-scm.com/doc)
- [Render Docs](https://render.com/docs)
- [Azure Docs](https://docs.microsoft.com/azure/)

---

**Last Updated**: April 2026
**Status**: Ready for Production ✅
**Next Steps**: Choose your deployment platform and follow the specific guide above
git add file1.php file2.php

# Commit with message
git commit -m "Brief description of changes"

# Push to GitHub
git push origin main
```

### Example Commits

```bash
# Add new feature
git commit -m "feat: Add product search functionality"

# Fix bug
git commit -m "fix: Correct form validation error messages"

# Update documentation
git commit -m "docs: Update deployment instructions"

# Refactor code
git commit -m "refactor: Simplify product filtering logic"
```

## 🌐 How to Access the Website

### Local Development

```
http://localhost/SMARTWATCHES/

Admin Panel:
http://localhost/SMARTWATCHES/admin.php

Contact Form:
http://localhost/SMARTWATCHES/contact.php
```

### Steps to Open

1. **Ensure XAMPP is Running**
   - Open XAMPP Control Panel
   - Click "Start" next to Apache
   - Click "Start" next to MySQL
   - Wait for both to show "Running"

2. **Open in Browser**
   ```
   http://localhost/SMARTWATCHES/
   ```

3. **View Admin Dashboard**
   ```
   http://localhost/SMARTWATCHES/admin.php
   ```

4. **Test Contact Form**
   Click "Contact" in navigation or visit:
   ```
   http://localhost/SMARTWATCHES/contact.php
   ```

## 🚢 Deploy to Render.com (Free Hosting)

### Prerequisites
- GitHub repository with code pushed
- Render.com account (free tier available)

### Deployment Steps

1. **Go to Render Dashboard**
   - Visit [render.com](https://render.com)
   - Sign up or login
   - Click "New +" → "Web Service"

2. **Connect GitHub**
   - Select "Build and deploy from a Git repository"
   - Search for `smartwatch-hub` repository
   - Click "Connect"

3. **Configure Service**
   - **Name**: `smartwatch-hub`
   - **Environment**: `PHP`
   - **Build Command**: Leave blank or use default
   - **Start Command**: `php -S 0.0.0.0:$PORT`
   - **Instance Type**: `Free` (for testing)

4. **Environment Variables**
   - Add from `.env.example`:
   ```
   DB_HOST=your_db_host
   DB_NAME=smartwatch_db
   DB_USER=root
   DB_PASS=your_password
   SITE_URL=https://smartwatch-hub.onrender.com/
   ENVIRONMENT=production
   DEBUG=false
   ```

5. **Deploy Database**
   - Use Render's PostgreSQL add-on (paid)
   - OR use third-party MySQL hosting: [db4free.net](https://www.db4free.net/)
   - Create database and import `schema.sql`

6. **Deploy**
   - Click "Create Web Service"
   - Render will automatically deploy on each GitHub push
   - Visit your live URL (shown in Render dashboard)

### Automatic Deployments
- Every `git push origin main` triggers automatic deployment
- View deployment logs in Render dashboard
- Rollback to previous versions if needed

## ☁️ Deploy to Microsoft Azure

### Prerequisites
- Azure account with free credits
- Visual Studio Code (optional but recommended)

### Deployment Steps

1. **Create App Service**
   - Go to [portal.azure.com](https://portal.azure.com)
   - Create "App Service"
   - Runtime: PHP 8.1
   - Region: Closest to you

2. **Configure PHP**
   - Set web root to `/public`
   - Enable "Always On" (for reliability)

3. **Create MySQL Database**
   - Create "Azure Database for MySQL"
   - Configure firewall to allow your IP
   - Create database and import `schema.sql`

4. **Deploy Code**
   - Option A: Git deployment
   ```bash
   az webapp deployment source config-zip \
     --resource-group myGroup \
     --name smartwatch-hub \
     --src archive.zip
   ```
   
   - Option B: FTP deployment
     - Get credentials from Azure portal
     - Use FTP client to upload files

5. **Configure App Settings**
   - Add environment variables via Azure Portal
   - Update database connection string

6. **Access Live Site**
   ```
   https://smartwatch-hub.azurewebsites.net/
   ```

## 🐳 Deploy with Docker

### Create Dockerfile

```dockerfile
FROM php:8.1-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Enable mod_rewrite
RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
```

### Create docker-compose.yml

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    environment:
      DB_HOST: mysql
      DB_NAME: smartwatch_db
      DB_USER: root
      DB_PASS: rootpassword
    depends_on:
      - mysql

  mysql:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: smartwatch_db
    volumes:
      - ./schema.sql:/docker-entrypoint-initdb.d/schema.sql
    ports:
      - "3306:3306"
```

### Run Docker

```bash
docker-compose up -d
# Access at http://localhost:8080
```

## 🔐 Production Checklist

- [ ] Set `DEBUG=false` in .env
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL certificate
- [ ] Set `ENVIRONMENT=production`
- [ ] Remove `.env` file (use environment variables)
- [ ] Set up regular database backups
- [ ] Configure firewall rules
- [ ] Set up monitoring/logging
- [ ] Verify all forms work correctly
- [ ] Test payment processing (if added)
- [ ] Load test the site
- [ ] Document configuration
- [ ] Set up email notifications

## 📊 Monitoring

### Render.com
- View logs: Dashboard → Service → Logs
- Check metrics: Logs & Metrics tab
- View deployment history: Manual deployments

### Azure
- Application Insights for monitoring
- View logs: Log stream
- Check performance metrics
- Set up alerts

### Local Development
- PHP error logs: `C:\xampp\apache\logs\`
- MySQL error logs: `C:\xampp\mysql\data\`
- Check browser console (F12) for JavaScript errors

## 🔄 Git Commands Reference

### Basic Operations
```bash
git status              # Show current status
git add .              # Stage all changes
git commit -m "msg"    # Commit with message
git push               # Push to GitHub
git pull               # Pull latest changes
```

### Branching
```bash
git branch             # List branches
git branch new-branch  # Create branch
git checkout branch    # Switch branch
git merge branch       # Merge branch
```

### Viewing History
```bash
git log                # View commit history
git log --oneline      # Short commit history
git diff               # See unstaged changes
git diff --staged      # See staged changes
```

### Undo Changes
```bash
git restore file.php   # Discard changes in file
git reset HEAD file    # Unstage file
git revert HEAD        # Undo last commit (safe)
```

## 📱 Testing After Deployment

### Functionality Tests
- [ ] Add product to cart
- [ ] Remove item from cart
- [ ] Proceed to checkout
- [ ] Submit contact form
- [ ] Check form validation
- [ ] View admin panel
- [ ] Add/edit/delete product (admin)
- [ ] Check product list updates

### Performance Tests
- [ ] Page load time < 3 seconds
- [ ] Mobile responsiveness
- [ ] Image loading
- [ ] Form submission
- [ ] Database queries (check slow logs)

### Security Tests
- [ ] CSRF tokens working
- [ ] Form validation active
- [ ] SQL injection protection
- [ ] XSS protection
- [ ] No database errors exposed

## 🆘 Troubleshooting

### Site Won't Load
1. Check XAMPP Apache status
2. Verify URL is correct
3. Check browser console (F12)
4. View PHP error logs

### Database Connection Error
1. Verify MySQL is running
2. Check .env credentials
3. Test connection manually
4. Check database user permissions

### Contact Form Not Working
1. Check PHP error logs
2. Verify user_submissions table exists
3. Test database write permission
4. Check form POST method

### Deployment Failed
1. View deployment logs on Render/Azure
2. Check environment variables
3. Verify database connection
4. Check composer/npm dependencies

## 📚 Helpful Resources

- **Git Guide**: https://git-scm.com/doc
- **GitHub Help**: https://docs.github.com
- **Render Docs**: https://render.com/docs
- **Azure Docs**: https://learn.microsoft.com/azure
- **Docker Guide**: https://docs.docker.com

## 🎯 Next Steps

1. Push to GitHub
2. Test locally
3. Deploy to Render/Azure
4. Set up monitoring
5. Share live URL
6. Gather user feedback
7. Continue development

---

**Questions?** Check the main [README.md](README.md) for more information.
