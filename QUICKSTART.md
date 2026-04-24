# 🚀 Quick Start Guide - SmartWatch Hub

## 5-Minute Setup (Local Development)

### Step 1: Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **"Start"** for Apache and MySQL
3. Wait for both to show green status

### Step 2: Access Application
```
http://localhost/SMARTWATCHES/
```

### Step 3: Test Features
- ✅ Browse products on home page
- ✅ Click on a product to see details
- ✅ Add product to cart
- ✅ Fill out contact form at `/contact.php`
- ✅ View submissions in admin panel at `/admin.php`

### Step 4: Add New Product (Admin)
1. Go to `http://localhost/SMARTWATCHES/admin.php`
2. Click **"Products"** tab
3. Click **"Add New Product"**
4. Fill out form and submit
5. View new product on home page

---

## 🐳 5-Minute Setup (Docker)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop) installed

### Steps
```powershell
cd c:\xampp\htdocs\SMARTWATCHES

# Start containers
docker-compose up -d

# Wait 30 seconds for MySQL to start
Start-Sleep -Seconds 30

# Access application
# http://localhost:8080
```

### Stop Docker
```powershell
docker-compose down
```

---

## 📤 Push to GitHub in 10 Minutes

### Step 1: Create GitHub Account
- Go to [GitHub.com](https://github.com)
- Sign up (free)
- Verify email

### Step 2: Create Repository
1. Go to [GitHub.com](https://github.com)
2. Click **"+"** (top right)
3. Click **"New repository"**
4. Name: `smartwatch-hub`
5. Choose **Public** or **Private**
6. Click **"Create repository"**

### Step 3: Configure Git (First Time Only)
```powershell
git config --global user.name "Your Name"
git config --global user.email "your.email@gmail.com"
```

### Step 4: Connect Repository
```powershell
cd c:\xampp\htdocs\SMARTWATCHES

# Add GitHub remote
git remote add origin https://github.com/YOUR_USERNAME/smartwatch-hub.git

# Verify
git remote -v
```

### Step 5: Push Code
```powershell
# Add all files
git add .

# Commit
git commit -m "Initial commit: Production-ready e-commerce platform"

# Push to GitHub
git push -u origin master
```

### Done! ✅
Your code is now on GitHub!

---

## ☁️ Deploy to Cloud (Choose One)

### Option 1: Render.com (Easiest)

1. Go to [render.com](https://render.com)
2. Click "New" → "Web Service"
3. Connect GitHub
4. Select `smartwatch-hub`
5. Set environment variables:
   ```
   DB_HOST=<render-mysql-host>
   DB_NAME=smartwatch_db
   DB_USER=smartwatch_user
   DB_PASS=<strong-password>
   SITE_URL=https://smartwatch-hub.onrender.com
   ENVIRONMENT=production
   DEBUG=false
   ```
6. Deploy!

### Option 2: Azure

1. Create [Azure](https://azure.microsoft.com) account
2. Create App Service (PHP)
3. Configure MySQL database
4. Connect GitHub
5. Deploy!

---

## 📝 Features to Test

- [ ] Homepage loads (shows products)
- [ ] Pagination works (5 products per page)
- [ ] Product detail page opens
- [ ] Add to cart works
- [ ] Cart updates quantity
- [ ] Remove from cart works
- [ ] Contact form validates (try empty fields)
- [ ] Contact form submits successfully
- [ ] Admin panel shows submissions
- [ ] Admin can add product
- [ ] Admin can edit product
- [ ] Admin can delete product
- [ ] Mobile responsive (resize window)
- [ ] Keyboard navigation (Tab key)
- [ ] Screen reader compatible (if using NVDA/JAWS)

---

## 🔐 Important Security Notes

1. **Never commit `.env` file** (it contains passwords!)
   - `.env` is protected by `.gitignore`
   - Use `.env.example` as template

2. **Change default passwords**
   - Database password (in production)
   - Admin access (add authentication)

3. **Enable HTTPS** (production only)
   - Use SSL certificates
   - Redirect HTTP to HTTPS

4. **Regular backups**
   - Backup database
   - Backup file system

---

## 📞 Common Commands

### Git Commands
```powershell
# Check status
git status

# View changes
git diff

# Stage files
git add .
git add specific-file.php

# Commit
git commit -m "Description of changes"

# Push
git push origin main

# Pull (if working on team)
git pull origin main

# View history
git log --oneline
```

### Docker Commands
```powershell
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Rebuild
docker-compose build

# Access MySQL
docker exec -it smartwatch-hub-mysql mysql -u smartwatch_user -p smartwatch_db
```

### XAMPP Commands (Windows)
```powershell
# Open XAMPP
C:\xampp\xampp-control.exe

# Stop services
C:\xampp\apache\bin\httpd -k stop
```

---

## 🆘 Troubleshooting

| Issue | Solution |
|-------|----------|
| Application won't start | Check Apache/MySQL running in XAMPP |
| "Cannot connect to database" | Check `.env` credentials |
| "Port 8080 already in use" | Change port in docker-compose.yml or stop other containers |
| "Git command not found" | Install Git from git-scm.com |
| "Files won't push to GitHub" | Check `.env` is in `.gitignore` |
| "500 Error" | Check PHP error logs in C:\xampp\apache\logs |
| "Form won't submit" | Check database connection and tables exist |

---

## 📚 Useful Links

- 📖 [PHP Documentation](https://www.php.net/manual/)
- 🗄️ [MySQL Documentation](https://dev.mysql.com/doc/)
- 🐙 [Git Documentation](https://git-scm.com/doc)
- 🚀 [Render Docs](https://render.com/docs)
- ☁️ [Azure Docs](https://docs.microsoft.com/azure/)
- 🐳 [Docker Docs](https://docs.docker.com/)
- ♿ [Accessibility Guide](https://www.w3.org/WAI/WCAG21/quickref/)

---

## ✅ Deployment Checklist

- [ ] Code committed to Git
- [ ] `.env` NOT in repository
- [ ] `.env.example` IS in repository
- [ ] Database auto-creates on first run
- [ ] All pages load without errors
- [ ] Contact form works
- [ ] Admin panel functions
- [ ] Environment variables documented
- [ ] README is up to date
- [ ] HTTPS enabled (production)
- [ ] Error logging configured
- [ ] Database backed up

---

**Version**: 1.0.0
**Last Updated**: April 2026
**Status**: Ready to Deploy ✅
