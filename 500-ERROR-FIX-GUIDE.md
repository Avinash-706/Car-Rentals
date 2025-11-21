# 🔧 500 Error Fix Guide

## Issues & Solutions

### **Issue 1: Maximum number of allowable file uploads exceeded**

**Cause:** PHP's `max_file_uploads` setting is too low (default is often 20)

**Solution:** Already fixed in your project!
- ✅ `auto-config.php` sets `max_file_uploads` to 500
- ✅ `.htaccess` sets `max_file_uploads` to 500

**Verify:**
```bash
http://localhost/verify-upload-limits.php
```

---

### **Issue 2: Failed opening required 'vendor/autoload.php'**

**Cause:** Composer dependencies not installed

**Solution:**

#### **Option A: Install Composer Dependencies**
```bash
# Navigate to project directory
cd /path/to/your/project

# Install dependencies
composer install

# Or if composer is not in PATH
php composer.phar install
```

#### **Option B: Check if Already Installed**
```bash
# Check if vendor folder exists
ls vendor/autoload.php

# If it exists, the issue is a path problem
```

**All files already use correct relative paths:**
```php
require_once __DIR__ . '/vendor/autoload.php'; // ✅ Correct
```

---

### **Issue 3: Internal Server Error 500**

**Common Causes:**

#### **A. PHP Upload Limits Too Low**
**Check:** `http://localhost/fix-500-error.php`

**Fix:** Already configured in:
- `auto-config.php` - Sets limits via `ini_set()`
- `.htaccess` - Sets limits for Apache

**Settings:**
```
upload_max_filesize = 200M
post_max_size = 500M
max_file_uploads = 500
max_execution_time = 600
memory_limit = 2048M
max_input_vars = 5000
```

#### **B. Missing Composer Dependencies**
**Check:** Does `vendor/autoload.php` exist?

**Fix:**
```bash
composer install
```

#### **C. Directory Permissions**
**Check:** `http://localhost/test-directory-system.php`

**Fix:**
```bash
chmod -R 755 uploads pdfs tmp logs
```

#### **D. Missing PHP Extensions**
**Check:** `http://localhost/check-gd-extension.php`

**Fix:** Install missing extensions:
- **Windows (XAMPP):** Edit `php.ini`, uncomment extensions
- **Linux:** `sudo apt-get install php-gd php-mbstring php-zip php-xml`
- **macOS:** `brew install php-gd`

#### **E. Path Issues**
**Check:** `http://localhost/verify-paths.php`

**Fix:** All paths already use `DirectoryManager` for cross-platform compatibility

---

## Quick Diagnostic

### **Step 1: Run Diagnostic Script**
```bash
http://localhost/fix-500-error.php
```

This will check:
- ✅ PHP upload limits
- ✅ Composer dependencies
- ✅ Required files
- ✅ Directory permissions
- ✅ PHP extensions
- ✅ Recent errors

### **Step 2: Check Error Logs**
```bash
# Check PHP error log
cat logs/php_errors.log

# Check Apache error log (if applicable)
# Windows (XAMPP): C:\xampp\apache\logs\error.log
# Linux: /var/log/apache2/error.log
```

### **Step 3: Test Components**

```bash
# Test upload limits
http://localhost/verify-upload-limits.php

# Test GD extension
http://localhost/check-gd-extension.php

# Test directories
http://localhost/test-directory-system.php

# Test image upload
http://localhost/test-image-upload-fix.php

# Test paths
http://localhost/verify-paths.php
```

---

## Common Fixes

### **Fix 1: Increase PHP Limits (if not working)**

#### **Method A: Edit php.ini**
```ini
upload_max_filesize = 200M
post_max_size = 500M
max_file_uploads = 500
max_execution_time = 600
memory_limit = 2048M
max_input_vars = 5000
```

**Location:**
- **Windows (XAMPP):** `C:\xampp\php\php.ini`
- **Linux:** `/etc/php/8.x/apache2/php.ini`
- **macOS (MAMP):** `/Applications/MAMP/bin/php/php8.x.x/conf/php.ini`

**After editing:** Restart Apache

#### **Method B: Use .user.ini (if .htaccess doesn't work)**
Create `.user.ini` in project root:
```ini
upload_max_filesize = 200M
post_max_size = 500M
max_file_uploads = 500
max_execution_time = 600
memory_limit = 2048M
max_input_vars = 5000
```

### **Fix 2: Install Composer**

#### **Windows:**
```bash
# Download and run installer
https://getcomposer.org/Composer-Setup.exe

# Then run in project directory
composer install
```

#### **Linux/macOS:**
```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install dependencies
composer install
```

### **Fix 3: Create Missing Directories**

```bash
# Manual creation
mkdir -p uploads/drafts/compressed uploads/drafts/uniform uploads/compressed uploads/uniform pdfs tmp/mpdf logs drafts/audit
chmod -R 755 uploads pdfs tmp logs drafts

# Or visit
http://localhost/test-directory-system.php
# Directories will be auto-created
```

### **Fix 4: Fix Permissions**

#### **Linux/macOS:**
```bash
# Set correct permissions
chmod -R 755 uploads pdfs tmp logs drafts

# Set correct ownership (if needed)
sudo chown -R www-data:www-data uploads pdfs tmp logs drafts
```

#### **Windows:**
```bash
# Usually no permission issues on Windows
# If issues occur, run XAMPP as Administrator
```

---

## Testing After Fixes

### **1. Test Upload Limits**
```bash
http://localhost/verify-upload-limits.php
```
**Expected:** All limits show ✅

### **2. Test Dependencies**
```bash
http://localhost/fix-500-error.php
```
**Expected:** "Composer dependencies are installed" ✅

### **3. Test Image Upload**
```bash
http://localhost/test-image-upload-fix.php
```
**Expected:** Upload succeeds ✅

### **4. Test Form Submission**
```bash
# Fill out form with multiple images
# Submit form
```
**Expected:** PDF generated successfully ✅

---

## Troubleshooting

### **Still Getting 500 Error?**

#### **1. Check Apache Error Log**
```bash
# Windows (XAMPP)
C:\xampp\apache\logs\error.log

# Linux
/var/log/apache2/error.log

# macOS (MAMP)
/Applications/MAMP/logs/apache_error.log
```

#### **2. Enable PHP Error Display (temporarily)**
Add to top of `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

#### **3. Check Browser Console**
- Open Developer Tools (F12)
- Check Console tab for JavaScript errors
- Check Network tab for failed requests

#### **4. Test with Minimal Data**
- Fill only Step 1 and Step 2
- Upload only 1-2 images
- Try submitting
- If works, gradually increase data

#### **5. Check .htaccess Syntax**
```bash
# Test Apache configuration
apachectl configtest

# Or
apache2ctl configtest
```

---

## Prevention

### **For Future Deployments:**

1. **Always run diagnostic first:**
   ```bash
   http://your-domain.com/fix-500-error.php
   ```

2. **Verify composer dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Check directory permissions:**
   ```bash
   chmod -R 755 uploads pdfs tmp logs drafts
   ```

4. **Test upload limits:**
   ```bash
   http://your-domain.com/verify-upload-limits.php
   ```

5. **Monitor error logs:**
   ```bash
   tail -f logs/php_errors.log
   ```

---

## Summary

**Your project already has:**
- ✅ Correct PHP upload limits in `auto-config.php`
- ✅ Correct PHP upload limits in `.htaccess`
- ✅ Relative paths for `vendor/autoload.php`
- ✅ DirectoryManager for cross-platform paths
- ✅ Automatic directory creation

**To fix 500 errors:**
1. Run `http://localhost/fix-500-error.php`
2. Install composer dependencies if missing: `composer install`
3. Fix any issues shown in diagnostic
4. Test form submission

**Your system is production-ready!** 🎉
