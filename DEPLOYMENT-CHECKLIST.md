# 🚀 Deployment Checklist - Run on Any Machine

## ✅ What You Need to Do on a New Machine

### **Step 1: Install Prerequisites** ⚠️ REQUIRED

#### **A. PHP (with required extensions)**
```bash
# Check PHP version (need 7.4+)
php -v

# Check if GD extension is installed
php -m | grep gd
```

**If PHP or GD is missing:**
- **Windows:** Install XAMPP or download PHP from php.net
- **Linux:** `sudo apt-get install php php-gd php-mbstring php-zip php-xml php-curl`
- **macOS:** `brew install php`

#### **B. Composer** ⚠️ REQUIRED
```bash
# Check if Composer is installed
composer --version
```

**If Composer is missing:**
- **Windows:** Download from https://getcomposer.org/Composer-Setup.exe
- **Linux/macOS:** 
  ```bash
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  ```

---

### **Step 2: Copy Project Files**

```bash
# Copy entire project folder to new machine
# Include ALL files and folders
```

**Important:** Make sure to copy:
- ✅ All PHP files
- ✅ `composer.json` and `composer.lock`
- ✅ `.htaccess` file
- ✅ `vendor/` folder (optional - can reinstall)
- ✅ Empty folders: `uploads/`, `pdfs/`, `tmp/`, `logs/`

---

### **Step 3: Install Dependencies** ⚠️ CRITICAL

```bash
# Navigate to project directory
cd /path/to/project

# Install Composer dependencies
composer install

# This will create/update the vendor/ folder
```

**Why this is needed:**
- The `vendor/` folder contains mPDF and PHPMailer libraries
- These are required for PDF generation and email sending
- Without this step, you'll get "Failed opening required 'vendor/autoload.php'" error

---

### **Step 4: Start PHP Server**

```bash
# Navigate to project directory
cd /path/to/project

# Start PHP built-in server
php -S localhost:8000

# Or use a different port
php -S localhost:8080
```

**Alternative:** Use XAMPP/WAMP/MAMP and place project in `htdocs/` folder

---

### **Step 5: Verify Setup** ✅ IMPORTANT

Open browser and visit:

```bash
# 1. Main diagnostic (checks everything)
http://localhost:8000/fix-500-error.php

# 2. Check GD extension
http://localhost:8000/check-gd-extension.php

# 3. Test directories
http://localhost:8000/test-directory-system.php

# 4. Verify paths
http://localhost:8000/verify-paths.php

# 5. Test upload
http://localhost:8000/test-image-upload-fix.php
```

**Expected Results:**
- ✅ All checks pass
- ✅ Directories auto-created
- ✅ GD extension available
- ✅ Upload works

---

### **Step 6: Configure Email (Optional)**

If you want email functionality, edit `config.php`:

```php
// Email Configuration (lines 18-26)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'your-email@gmail.com');
define('SMTP_TO_EMAIL', 'recipient@gmail.com');
```

---

## 📋 Quick Start Commands

### **For New Machine (Complete Setup):**

```bash
# 1. Check prerequisites
php -v
composer --version
php -m | grep gd

# 2. Navigate to project
cd /path/to/project

# 3. Install dependencies
composer install

# 4. Start server
php -S localhost:8000

# 5. Open browser
http://localhost:8000/fix-500-error.php
```

---

## ⚠️ Common Issues on New Machine

### **Issue 1: "composer: command not found"**

**Solution:** Install Composer (see Step 1B above)

### **Issue 2: "Failed opening required 'vendor/autoload.php'"**

**Solution:** Run `composer install` in project directory

### **Issue 3: "Call to undefined function imagecreatefrompng()"**

**Solution:** Install GD extension
- **Windows (XAMPP):** Edit `php.ini`, uncomment `extension=gd`
- **Linux:** `sudo apt-get install php-gd`
- **macOS:** `brew install php-gd`

Then restart PHP server.

### **Issue 4: "Permission denied" on uploads**

**Solution:** 
```bash
# Linux/macOS
chmod -R 755 uploads pdfs tmp logs drafts

# Windows - usually no issue, but run as Administrator if needed
```

### **Issue 5: Directories not created**

**Solution:** Visit `http://localhost:8000/test-directory-system.php`
- Directories will be auto-created
- Check if they're writable

---

## 🎯 What Works Automatically

Your project is designed to work cross-platform with minimal setup:

### **✅ Automatic Features:**
1. **Directory Creation** - All required directories auto-created on first run
2. **Path Resolution** - Works on Windows/Linux/macOS automatically
3. **PHP Configuration** - Upload limits set automatically via `auto-config.php`
4. **Error Handling** - Graceful fallbacks if something fails

### **✅ No Manual Configuration Needed:**
- ❌ No need to edit paths
- ❌ No need to create directories manually
- ❌ No need to configure PHP settings (if .htaccess works)
- ❌ No need to change code

### **⚠️ Only 2 Things Required:**
1. **Install Composer dependencies:** `composer install`
2. **Have GD extension installed:** Check with `php -m | grep gd`

---

## 📦 What to Share

### **Option A: Share Without vendor/ (Smaller)**

**Files to share:**
```
project/
├── All .php files
├── composer.json
├── composer.lock
├── .htaccess
├── script.js
├── style.css
└── (empty folders will be auto-created)
```

**Recipient must run:**
```bash
composer install
```

**Size:** ~5-10 MB

### **Option B: Share With vendor/ (Larger, Ready to Run)**

**Files to share:**
```
project/
├── All .php files
├── composer.json
├── composer.lock
├── .htaccess
├── script.js
├── style.css
└── vendor/ (entire folder)
```

**Recipient can run immediately:**
```bash
php -S localhost:8000
```

**Size:** ~50-100 MB

---

## 🔍 Verification Checklist

After setup on new machine, verify:

- [ ] PHP 7.4+ installed: `php -v`
- [ ] Composer installed: `composer --version`
- [ ] GD extension loaded: `php -m | grep gd`
- [ ] Dependencies installed: `ls vendor/autoload.php`
- [ ] Server running: `php -S localhost:8000`
- [ ] Diagnostic passes: `http://localhost:8000/fix-500-error.php`
- [ ] Directories created: `http://localhost:8000/test-directory-system.php`
- [ ] Upload works: `http://localhost:8000/test-image-upload-fix.php`
- [ ] Form loads: `http://localhost:8000/`

---

## 🎉 Summary

**To run on a new machine:**

```bash
# 1. Ensure PHP + Composer + GD extension installed
php -v && composer --version && php -m | grep gd

# 2. Copy project files

# 3. Install dependencies
cd project
composer install

# 4. Start server
php -S localhost:8000

# 5. Verify
# Open: http://localhost:8000/fix-500-error.php
```

**That's it!** Your project will:
- ✅ Auto-create all directories
- ✅ Auto-configure PHP settings
- ✅ Work on Windows/Linux/macOS
- ✅ Handle all paths correctly
- ✅ Generate PDFs successfully

**The only manual step is:** `composer install` (if vendor/ folder not included)

---

## 📞 Quick Help

**If something doesn't work:**

1. Visit: `http://localhost:8000/fix-500-error.php`
2. Check what's marked with ❌
3. Follow the fix instructions shown
4. Refresh and verify ✅

**Your project is 99% ready for any machine!** 🚀
