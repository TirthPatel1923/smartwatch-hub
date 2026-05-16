# Azure Deployment Guide for SMARTWATCHES (Student Account)

## Overview
This guide walks you through deploying your PHP + MySQL app to Azure using your free student account. You'll use:
- **Azure Web App** for PHP hosting
- **Azure Database for MySQL** for the database
- **GitHub Actions** for automatic deployment

---

## Step 1: Login to Azure Portal

1. Go to https://portal.azure.com
2. Sign in with your Azure student account credentials
3. You should see your free credits in the top-right corner (typically $100/month for 12 months)

---

## Step 2: Create a Resource Group

A Resource Group organizes all your resources in one place.

### Option A: Portal (Easiest for beginners)
1. Search for **"Resource groups"** in the search bar
2. Click **+ Create**
3. Fill in:
   - **Subscription**: Azure for Students
   - **Resource group name**: `smartwatch-rg`
   - **Region**: Choose closest to you (e.g., `East US`, `Central US`)
4. Click **Review + Create** → **Create**

### Option B: Azure CLI (Faster)
```bash
az login
az group create --name smartwatch-rg --location eastus
```

---

## Step 3: Create Azure Database for MySQL (Flexible Server)

MySQL Flexible Server is free-tier eligible and recommended.

### Option A: Portal
1. Search **"Azure Database for MySQL"**
2. Click **Create** → Choose **Flexible Server**
3. Fill in:
   - **Subscription**: Azure for Students
   - **Resource Group**: `smartwatch-rg`
   - **Server name**: `smartwatch-mysql` (must be unique globally)
   - **Region**: Same as your resource group (e.g., `East US`)
   - **MySQL version**: `8.0`
   - **Compute + storage**: Select **Burstable** tier (B1s or similar — cheapest)
   - **Authentication method**: MySQL authentication (username + password)
   - **Admin username**: `mysqladmin` (or your choice)
   - **Password**: Create a strong password (you'll need this later)
   - Check "Allow public access from Azure services and resources within Azure to access this server"
4. Click **Review + Create** → **Create** (wait 2-3 minutes)

### Option B: Azure CLI
```bash
az mysql flexible-server create \
  --resource-group smartwatch-rg \
  --name smartwatch-mysql \
  --admin-user mysqladmin \
  --admin-password "YourStrongPassword123!" \
  --location eastus \
  --tier Burstable \
  --sku-name Standard_B1s \
  --storage-size 32 \
  --public-access Enabled
```

### After MySQL is created:
1. Go to your MySQL server in portal
2. Go to **Networking** → Under "Firewall rules", enable **"Allow public access from Azure services and resources within Azure"**
3. Create the database:
   - Click **Databases** → **+ Create**
   - Name: `smartwatch_db`
   - Click **Create**

---

## Step 4: Get Your MySQL Connection String

You'll need this to configure your Web App.

1. In Azure Portal, go to **Azure Database for MySQL** → your server (`smartwatch-mysql`)
2. Click **Connection strings**
3. Copy the **PHP** connection string (or build it manually):
   ```
   mysql://mysqladmin:YourStrongPassword123!@smartwatch-mysql.mysql.database.azure.com:3306/smartwatch_db
   ```
4. Keep this safe — you'll use it in Step 6

---

## Step 5: Create Azure Web App (PHP Runtime)

### Option A: Portal
1. Search **"Web App"**
2. Click **Create**
3. Fill in:
   - **Subscription**: Azure for Students
   - **Resource Group**: `smartwatch-rg`
   - **Name**: `smartwatch-app-[random]` (must be globally unique; e.g., `smartwatch-app-tirth`)
   - **Publish**: Code
   - **Runtime stack**: PHP 8.1 (or 8.2)
   - **Operating System**: Linux
   - **Region**: Same as your database (e.g., `East US`)
   - **App Service Plan**: 
     - Click **Create new**
     - Name: `smartwatch-plan`
     - **Sku and size**: Click "Change size" → Select **Free Tier (F1)** or **Basic Tier (B1)** if Free is unavailable
4. Click **Review + Create** → **Create** (wait 1-2 minutes)

### Option B: Azure CLI
```bash
# Create App Service Plan
az appservice plan create \
  --name smartwatch-plan \
  --resource-group smartwatch-rg \
  --sku FREE \
  --is-linux

# Create Web App
az webapp create \
  --resource-group smartwatch-rg \
  --plan smartwatch-plan \
  --name smartwatch-app-tirth \
  --runtime "PHP|8.1"
```

---

## Step 6: Configure App Settings (Environment Variables)

Your `config.php` reads environment variables. Configure them here.

1. In Azure Portal, go to your **Web App** (`smartwatch-app-tirth`)
2. Click **Configuration** (left sidebar)
3. Click **+ New application setting** and add each:
   - **Name**: `DATABASE_URL` 
   - **Value**: `mysql://mysqladmin:YourStrongPassword123!@smartwatch-mysql.mysql.database.azure.com:3306/smartwatch_db`
4. Add another:
   - **Name**: `SITE_URL`
   - **Value**: `https://smartwatch-app-tirth.azurewebsites.net` (replace with your app name)
5. Click **Save** (the app will restart)

---

## Step 7: Deploy Your App Using GitHub Actions

This sets up automatic deployment: every time you push to GitHub, Azure builds and deploys.

### 7A: Get Publish Profile from Azure

1. In your **Web App** → **Get publish profile** (top-right button)
2. This downloads an XML file — open it in notepad
3. Copy the **entire content** (starts with `<?xml` and ends with `</publishProfile>`)

### 7B: Add Publish Profile to GitHub Secrets

1. Go to your GitHub repo: https://github.com/TirthPatel1923/smartwatch-hub
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. **Name**: `AZURE_WEBAPP_PUBLISH_PROFILE`
5. **Value**: Paste the XML content from Step 7A
6. Click **Add secret**

### 7C: Create GitHub Actions Workflow

I'll create this file for you in a moment, which will:
- Build your PHP app
- Run composer install
- Deploy to Azure on every push to `master`

---

## Step 8: Import Your Database Schema

You need to populate the MySQL database with your tables.

### Option A: From Azure Portal (Easiest)
1. In Azure Portal, go to your **Web App**
2. Click **SSH** (left sidebar) to open terminal
3. Run:
   ```bash
   cd /home/site/wwwroot
   php db.php
   ```
   This will auto-create all tables and seed admin user.

### Option B: Using MySQL CLI (If you have MySQL installed locally)
```bash
mysql -h smartwatch-mysql.mysql.database.azure.com -u mysqladmin -p smartwatch_db < smartwatch_db.sql
```
(When prompted, enter your MySQL password)

### Option C: Via PHP Script
- Copy `smartwatch_db.sql` or run your `db.php` initialization once the app is deployed

---

## Step 9: Set Up GitHub Actions Workflow

I'll create the workflow file for you. Once pushed, it will auto-deploy on every commit.

**File location**: `.github/workflows/azure-deploy.yml`

---

## Step 10: Deploy & Test

1. **Push code to GitHub**:
   ```bash
   git push origin master
   ```

2. **Watch deployment**:
   - Go to your GitHub repo → **Actions**
   - You should see a workflow running
   - Wait for it to complete (usually 2-3 minutes)

3. **Verify app is live**:
   - Visit: `https://smartwatch-app-tirth.azurewebsites.net` (replace with your app name)
   - Check health endpoint: `https://smartwatch-app-tirth.azurewebsites.net/render-healthcheck.php`
   - Test login at: `https://smartwatch-app-tirth.azurewebsites.net/login.php`

4. **Check logs if there's an issue**:
   - Web App → **Log stream** to see errors

---

## Student Account Benefits

- **Free credits**: $100/month for 12 months
- **Free services**: Web App (Standard Tier), Database (first 12 months)
- **No credit card required** for free tier
- **After 12 months**: Services scale down or you pay as you go

---

## Quick Reference: Azure CLI Commands (All at once)

If you prefer command-line, paste this into PowerShell after `az login`:

```bash
# 1. Create Resource Group
az group create --name smartwatch-rg --location eastus

# 2. Create MySQL Server (Flexible)
az mysql flexible-server create `
  --resource-group smartwatch-rg `
  --name smartwatch-mysql `
  --admin-user mysqladmin `
  --admin-password "YourStrongPassword123!" `
  --location eastus `
  --tier Burstable `
  --sku-name Standard_B1s

# 3. Create Database
az mysql flexible-server db create `
  --resource-group smartwatch-rg `
  --server-name smartwatch-mysql `
  --database-name smartwatch_db

# 4. Create App Service Plan (Free)
az appservice plan create `
  --name smartwatch-plan `
  --resource-group smartwatch-rg `
  --sku FREE `
  --is-linux

# 5. Create Web App
az webapp create `
  --resource-group smartwatch-rg `
  --plan smartwatch-plan `
  --name smartwatch-app-tirth `
  --runtime "PHP|8.1"

# 6. Set App Settings
az webapp config appsettings set `
  --resource-group smartwatch-rg `
  --name smartwatch-app-tirth `
  --settings DATABASE_URL="mysql://mysqladmin:YourStrongPassword123!@smartwatch-mysql.mysql.database.azure.com:3306/smartwatch_db" `
           SITE_URL="https://smartwatch-app-tirth.azurewebsites.net"
```

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| **502 Bad Gateway** | Check Log stream; ensure DB connection string is correct in App Settings |
| **Database connection failed** | Verify MySQL firewall allows Azure services; test connection string |
| **Deployment fails** | Check GitHub Actions logs; ensure `composer.json` is in repo root |
| **App stuck on loading** | SSH into Web App and check `/home/site/wwwroot/` for errors; view Log stream |

---

## Next Steps After Deployment

1. **Set up custom domain** (optional): Web App → Custom domains
2. **Enable HTTPS**: Already enabled by default (*.azurewebsites.net)
3. **Monitor costs**: Azure Portal → Cost Management
4. **Scale up if needed**: Change App Service Plan to pay-as-you-go after free tier ends

---

**You're ready to deploy! Let me create the GitHub Actions workflow file for you.**
