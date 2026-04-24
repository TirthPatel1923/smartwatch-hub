# 📤 How to Push Your Code to GitHub

Complete step-by-step guide to deploy SmartWatch Hub to GitHub.

---

## 🎯 Prerequisites

1. **GitHub Account** (free) - [Sign up at GitHub.com](https://github.com)
2. **Git Installed** - [Download from git-scm.com](https://git-scm.com/download/win)
3. **PowerShell** (comes with Windows)
4. **SSH or HTTPS Access** to GitHub

---

## 📋 Step 1: Configure Git (First Time Only)

Open PowerShell and run:

```powershell
# Set your name
git config --global user.name "Your Full Name"

# Set your email (use the same email as your GitHub account)
git config --global user.email "your.email@gmail.com"

# Verify configuration
git config --global --list
```

You should see output like:
```
user.name=Your Full Name
user.email=your.email@gmail.com
```

---

## 🔑 Step 2: Create GitHub Personal Access Token (HTTPS Method)

Using HTTPS with a Personal Access Token is easier than SSH.

1. Go to [GitHub.com](https://github.com) and sign in
2. Click your **Profile** (top right) → **Settings**
3. Go to **Developer settings** → **Personal access tokens** → **Tokens (classic)**
4. Click **"Generate new token"** → **"Generate new token (classic)"**
5. Name: `smartwatch-hub-token`
6. Select scopes:
   - ✅ `repo` (full control of private repositories)
7. Click **"Generate token"**
8. **Copy the token** - you'll only see it once!
   ```
   ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```

**Save this token securely** - use it when pushing code.

---

## 🏗️ Step 3: Create GitHub Repository

1. Go to [GitHub.com](https://github.com)
2. Click **"+"** (top right)
3. Select **"New repository"**
4. Fill in details:
   ```
   Repository name: smartwatch-hub
   Description: Production-ready e-commerce platform for smartwatches
   Visibility: Choose Public or Private
   Initialize repository: LEAVE UNCHECKED (do not add README, gitignore, license)
   ```
5. Click **"Create repository"**

**You'll see a page with commands to run. Follow the next steps.**

---

## 🔗 Step 4: Connect Local Repository to GitHub

GitHub shows you some commands. Here's what to run:

```powershell
cd c:\xampp\htdocs\SMARTWATCHES

# Add GitHub as remote (use your USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/smartwatch-hub.git

# Verify remote was added
git remote -v
```

**Expected output:**
```
origin  https://github.com/YOUR_USERNAME/smartwatch-hub.git (fetch)
origin  https://github.com/YOUR_USERNAME/smartwatch-hub.git (push)
```

---

## ✅ Step 5: Verify Commit is Ready

```powershell
# Check status
git status
```

**Expected output:**
```
On branch master
nothing to commit, working tree clean
```

If you see changes, commit them first:
```powershell
git add .
git commit -m "Latest updates"
```

---

## 🚀 Step 6: Push to GitHub

```powershell
git push -u origin master
```

**What happens:**
1. You'll be prompted: `Username for 'https://github.com':`
   - Type: `YOUR_GITHUB_USERNAME`

2. Then: `Password for 'https://github.com/YOUR_USERNAME':`
   - Paste your Personal Access Token (Ctrl+V)
   - It won't show as you type (that's normal)

3. Press Enter

**Expected output:**
```
Counting objects: 50, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (45/45), done.
Writing objects: 100% (50/50), 100 KB | 500 KiB/s, done.
Total 50 (delta 10), reused 0 (delta 0)
remote: Resolving deltas: 100% (10/10), done.
To https://github.com/YOUR_USERNAME/smartwatch-hub.git
 * [new branch]      master -> master
Branch 'master' set to track remote branch 'master' from 'origin'.
```

✅ **Your code is now on GitHub!**

---

## 🔍 Step 7: Verify on GitHub

1. Go to [GitHub.com](https://github.com)
2. Go to your repositories
3. Click `smartwatch-hub`
4. You should see all your files:
   - ✅ `index.php`
   - ✅ `admin.php`
   - ✅ `contact.php`
   - ✅ `README.md`
   - ✅ `DEPLOYMENT.md`
   - ✅ `.env.example`
   - ✅ `Dockerfile`
   - ❌ NO `.env` file (protected by .gitignore) ✓

---

## ⚙️ Step 8: Store GitHub Credentials (Optional)

To avoid typing credentials every time:

```powershell
# Enable credential caching for 1 hour
git config --global credential.helper cache --timeout=3600

# Or store permanently (only on personal computer)
git config --global credential.helper store
```

---

## 🔄 Future Workflow: Making Updates

After making changes locally:

```powershell
cd c:\xampp\htdocs\SMARTWATCHES

# Check what changed
git status

# Stage changes
git add .

# Or stage specific files
git add admin.php functions.php

# Commit changes
git commit -m "Description of your changes"

# Push to GitHub
git push origin master
```

---

## 📌 Important Git Commands

```powershell
# Check repository status
git status

# View commit history
git log --oneline -5

# View what changed
git diff

# Undo last commit (but keep changes)
git reset --soft HEAD~1

# Undo changes to a file
git restore filename.php

# Create a backup branch
git branch backup-2026-04-24
```

---

## ⚠️ Troubleshooting

### Error: "fatal: 'origin' does not appear to be a git repository"
```powershell
# Make sure you're in the correct directory
cd c:\xampp\htdocs\SMARTWATCHES
git remote -v
```

### Error: "fatal: The current branch master has no upstream branch"
```powershell
# First time pushing - use -u flag
git push -u origin master
```

### Error: "Permission denied (publickey)" or "Authentication failed"
```powershell
# Your token may be expired or incorrect
# Create a new Personal Access Token and try again
git push origin master
# When prompted, paste the new token
```

### Error: "fatal: unable to access 'https://github.com/...': The requested URL returned error: 403"
```powershell
# Username or token incorrect
# Verify you're using:
# - Correct GitHub USERNAME (not email)
# - Valid Personal Access Token (not your password)
```

### My changes aren't showing on GitHub
```powershell
# Verify you pushed successfully
git log --oneline  # Check commits are there
git remote -v      # Check origin points to GitHub
git status         # Check everything is committed
git push origin master  # Push again
```

---

## 🎓 Understanding Git

**Local Repository** (on your computer)
- Your files
- `.git` folder (contains Git history)

**Remote Repository** (on GitHub)
- Copy of your files
- Backup of your work
- Shareable with others

**Workflow:**
1. Edit files locally
2. Stage changes: `git add .`
3. Commit changes: `git commit -m "message"`
4. Push to GitHub: `git push origin master`

---

## 🔐 Security Best Practices

✅ **DO:**
- Keep Personal Access Token secure
- Use strong GitHub password
- Enable two-factor authentication on GitHub
- Never commit `.env` files
- Keep `.gitignore` updated

❌ **DON'T:**
- Share your Personal Access Token
- Commit passwords or API keys
- Use `--allow-empty-message` in commits
- Force push to main branch without reason
- Grant unnecessary permissions to tokens

---

## 📊 GitHub Repository Tips

### Add Collaboration
1. Go to repository
2. **Settings** → **Collaborators**
3. Click **"Add people"**
4. Enter GitHub username

### Enable Issues
1. **Settings** → **Features**
2. Enable **"Issues"**
3. Users can report bugs

### Add License
1. Go to repository
2. Click **"Add file"** → **"Create new file"**
3. Name: `LICENSE`
4. Choose a template (MIT, Apache, etc.)

### Add .gitignore (if not present)
Already included in the project!

---

## 🌐 Next Steps: Deploy to Cloud

After pushing to GitHub:

### Option 1: Render.com (Easiest)
```
1. Go to render.com
2. Sign up with GitHub
3. Create Web Service
4. Select your smartwatch-hub repository
5. Deploy!
```

### Option 2: Azure
```
1. Create Azure account
2. Create App Service
3. Connect GitHub repository
4. Configure database
5. Deploy!
```

See `DEPLOYMENT.md` for detailed instructions.

---

## ✅ Checklist

Before pushing:
- [ ] All files saved
- [ ] Code tested locally
- [ ] `.env` NOT included
- [ ] `.env.example` IS included
- [ ] No passwords in code
- [ ] Git configured with correct name/email
- [ ] GitHub repository created
- [ ] Remote added: `git remote -v`
- [ ] Changes committed: `git log`
- [ ] Ready to push: `git push origin master`

After pushing:
- [ ] GitHub shows all files
- [ ] README displays correctly
- [ ] License file present (if added)
- [ ] `.env` NOT visible
- [ ] Commit history visible
- [ ] Share repository link

---

## 📞 Help & Resources

- **GitHub Docs**: https://docs.github.com/en/get-started
- **Git Tutorial**: https://git-scm.com/book/en/v2
- **GitHub Desktop** (GUI): https://desktop.github.com/

---

## 🎉 You're All Set!

Your code is now on GitHub and ready for:
- ✅ Backup
- ✅ Version control
- ✅ Collaboration
- ✅ Cloud deployment
- ✅ Sharing with others

**Your GitHub URL:**
```
https://github.com/YOUR_USERNAME/smartwatch-hub
```

**Share this link to showcase your work!**

---

**Last Updated**: April 2026  
**Questions?** Check the troubleshooting section above
