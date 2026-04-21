# SmartWatch Hub - Deployment & Git Guide

## 🚀 How to Push to GitHub

### Step 1: Create GitHub Repository

1. Go to [GitHub.com](https://github.com) and sign in
2. Click **"New"** button (top left)
3. Repository name: `smartwatch-hub`
4. Description: "Full-featured e-commerce platform for smartwatches"
5. Choose **Public** or **Private**
6. **DO NOT** initialize with README, .gitignore, or license (we have these)
7. Click **"Create repository"**

### Step 2: Connect Local Repository to GitHub

After creating the GitHub repo, you'll see commands. Run these in PowerShell:

```bash
cd c:\xampp\htdocs\SMARTWATCHES

# Add remote origin
git remote add origin https://github.com/YOUR_USERNAME/smartwatch-hub.git

# Rename branch to main (if needed)
git branch -M main

# Push to GitHub
git push -u origin main
```

### Step 3: Verify on GitHub

1. Refresh your GitHub repository page
2. You should see all your project files
3. Verify `.env.example` is there (but NOT `.env`)
4. Verify `.gitignore` is protecting sensitive files

## 📝 Git Workflow

### Making Changes

```bash
# Check status
git status

# Stage changes
git add .

# Or stage specific files
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
