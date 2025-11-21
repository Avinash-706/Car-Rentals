<?php
/**
 * 500 Error Diagnostic and Fix Script
 * Checks and fixes common issues causing Internal Server Error
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>500 Error Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #2196F3; }
        .check { padding: 15px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #ccc; }
        .pass { background: #d4edda; border-left-color: #28a745; }
        .fail { background: #f8d7da; border-left-color: #dc3545; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .icon { font-size: 20px; margin-right: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .fix-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .fix-btn:hover { background: #218838; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 500 Error Diagnostic & Fix</h1>
    
    <h2>1. PHP Upload Limits Check</h2>
    <?php
    $uploadLimits = [
        'upload_max_filesize' => ['current' => ini_get('upload_max_filesize'), 'required' => '200M'],
        'post_max_size' => ['current' => ini_get('post_max_size'), 'required' => '500M'],
        'max_file_uploads' => ['current' => ini_get('max_file_uploads'), 'required' => '500'],
        'max_execution_time' => ['current' => ini_get('max_execution_time'), 'required' => '600'],
        'memory_limit' => ['current' => ini_get('memory_limit'), 'required' => '2048M'],
        'max_input_vars' => ['current' => ini_get('max_input_vars'), 'required' => '5000']
    ];
    
    $allLimitsOk = true;
    
    echo "<table>";
    echo "<tr><th>Setting</th><th>Current</th><th>Required</th><th>Status</th></tr>";
    
    foreach ($uploadLimits as $setting => $values) {
        $current = $values['current'];
        $required = $values['required'];
        
        // Convert to bytes for comparison
        $currentBytes = convertToBytes($current);
        $requiredBytes = convertToBytes($required);
        
        $ok = $currentBytes >= $requiredBytes;
        $icon = $ok ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
        
        if (!$ok) $allLimitsOk = false;
        
        echo "<tr>";
        echo "<td><code>$setting</code></td>";
        echo "<td><code>$current</code></td>";
        echo "<td><code>$required</code></td>";
        echo "<td>$icon</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    if ($allLimitsOk) {
        echo "<div class='check pass'><span class='icon'>✅</span><strong>All upload limits are configured correctly</strong></div>";
    } else {
        echo "<div class='check fail'><span class='icon'>❌</span><strong>Some upload limits are too low</strong></div>";
        echo "<div class='check warning'>";
        echo "<strong>How to fix:</strong><br>";
        echo "1. Check if <code>auto-config.php</code> is included at the top of all entry files<br>";
        echo "2. Verify <code>.htaccess</code> has correct PHP settings<br>";
        echo "3. If on shared hosting, contact your host to increase limits<br>";
        echo "4. For local development (XAMPP/WAMP), edit <code>php.ini</code>";
        echo "</div>";
    }
    ?>
    
    <h2>2. Composer Dependencies Check</h2>
    <?php
    $vendorExists = file_exists(__DIR__ . '/vendor/autoload.php');
    $composerJsonExists = file_exists(__DIR__ . '/composer.json');
    
    if ($vendorExists) {
        echo "<div class='check pass'><span class='icon'>✅</span><strong>Composer dependencies are installed</strong></div>";
        echo "<div class='check info'>";
        echo "Vendor folder: <code>" . __DIR__ . "/vendor/</code><br>";
        echo "Autoload file: <code>" . __DIR__ . "/vendor/autoload.php</code>";
        echo "</div>";
    } else {
        echo "<div class='check fail'><span class='icon'>❌</span><strong>Composer dependencies are NOT installed</strong></div>";
        echo "<div class='check warning'>";
        echo "<strong>How to fix:</strong><br>";
        if ($composerJsonExists) {
            echo "1. Open terminal in project directory<br>";
            echo "2. Run: <code>composer install</code><br>";
            echo "3. Wait for dependencies to download<br>";
            echo "4. Refresh this page";
        } else {
            echo "❌ <code>composer.json</code> is missing! Cannot install dependencies.";
        }
        echo "</div>";
    }
    ?>
    
    <h2>3. Required Files Check</h2>
    <?php
    $requiredFiles = [
        'auto-config.php' => 'PHP configuration',
        'init-directories.php' => 'Directory management',
        'config.php' => 'Application config',
        'generate-pdf.php' => 'PDF generation',
        'submit.php' => 'Form submission',
        'upload-image.php' => 'Image upload',
        'image-optimizer.php' => 'Image processing',
        'vendor/autoload.php' => 'Composer autoload'
    ];
    
    echo "<table>";
    echo "<tr><th>File</th><th>Description</th><th>Status</th></tr>";
    
    $allFilesExist = true;
    foreach ($requiredFiles as $file => $description) {
        $exists = file_exists(__DIR__ . '/' . $file);
        $icon = $exists ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
        
        if (!$exists) $allFilesExist = false;
        
        echo "<tr>";
        echo "<td><code>$file</code></td>";
        echo "<td>$description</td>";
        echo "<td>$icon</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    if ($allFilesExist) {
        echo "<div class='check pass'><span class='icon'>✅</span><strong>All required files exist</strong></div>";
    } else {
        echo "<div class='check fail'><span class='icon'>❌</span><strong>Some required files are missing</strong></div>";
    }
    ?>
    
    <h2>4. Directory Permissions Check</h2>
    <?php
    $requiredDirs = [
        'uploads',
        'uploads/drafts',
        'uploads/compressed',
        'uploads/uniform',
        'pdfs',
        'tmp',
        'logs'
    ];
    
    echo "<table>";
    echo "<tr><th>Directory</th><th>Exists</th><th>Writable</th><th>Status</th></tr>";
    
    $allDirsOk = true;
    foreach ($requiredDirs as $dir) {
        $fullPath = __DIR__ . '/' . $dir;
        $exists = file_exists($fullPath);
        $writable = $exists && is_writable($fullPath);
        
        $existsIcon = $exists ? "✅" : "❌";
        $writableIcon = $writable ? "✅" : "❌";
        $status = ($exists && $writable) ? "<span style='color: #28a745;'>OK</span>" : "<span style='color: #dc3545;'>ISSUE</span>";
        
        if (!$exists || !$writable) $allDirsOk = false;
        
        echo "<tr>";
        echo "<td><code>$dir</code></td>";
        echo "<td>$existsIcon</td>";
        echo "<td>$writableIcon</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    if ($allDirsOk) {
        echo "<div class='check pass'><span class='icon'>✅</span><strong>All directories exist and are writable</strong></div>";
    } else {
        echo "<div class='check fail'><span class='icon'>❌</span><strong>Some directories have issues</strong></div>";
        echo "<div class='check warning'>";
        echo "<strong>How to fix:</strong><br>";
        echo "Run: <code>chmod -R 755 uploads pdfs tmp logs</code><br>";
        echo "Or visit: <code>test-directory-system.php</code> to auto-create directories";
        echo "</div>";
    }
    ?>
    
    <h2>5. PHP Extensions Check</h2>
    <?php
    $requiredExtensions = [
        'gd' => 'Image processing',
        'mbstring' => 'String handling',
        'zip' => 'ZIP operations',
        'xml' => 'XML parsing',
        'curl' => 'HTTP requests',
        'json' => 'JSON handling'
    ];
    
    echo "<table>";
    echo "<tr><th>Extension</th><th>Description</th><th>Status</th></tr>";
    
    $allExtensionsLoaded = true;
    foreach ($requiredExtensions as $ext => $description) {
        $loaded = extension_loaded($ext);
        $icon = $loaded ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
        
        if (!$loaded) $allExtensionsLoaded = false;
        
        echo "<tr>";
        echo "<td><code>$ext</code></td>";
        echo "<td>$description</td>";
        echo "<td>$icon</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    if ($allExtensionsLoaded) {
        echo "<div class='check pass'><span class='icon'>✅</span><strong>All required PHP extensions are loaded</strong></div>";
    } else {
        echo "<div class='check fail'><span class='icon'>❌</span><strong>Some PHP extensions are missing</strong></div>";
        echo "<div class='check warning'>";
        echo "<strong>How to fix:</strong><br>";
        echo "Visit: <code>check-gd-extension.php</code> for detailed installation instructions";
        echo "</div>";
    }
    ?>
    
    <h2>6. Error Log Check</h2>
    <?php
    $errorLogPath = __DIR__ . '/logs/php_errors.log';
    
    if (file_exists($errorLogPath)) {
        $errorLog = file_get_contents($errorLogPath);
        $lines = explode("\n", trim($errorLog));
        $recentErrors = array_slice($lines, -10); // Last 10 errors
        
        if (!empty($errorLog)) {
            echo "<div class='check warning'><span class='icon'>⚠️</span><strong>Recent errors found in log</strong></div>";
            echo "<div class='check info'>";
            echo "<strong>Last 10 errors:</strong><br>";
            echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 4px; overflow: auto; max-height: 300px;'>";
            echo htmlspecialchars(implode("\n", $recentErrors));
            echo "</pre>";
            echo "</div>";
        } else {
            echo "<div class='check pass'><span class='icon'>✅</span><strong>No errors in log</strong></div>";
        }
    } else {
        echo "<div class='check info'><span class='icon'>ℹ️</span><strong>Error log file not found (this is OK if no errors occurred)</strong></div>";
    }
    ?>
    
    <h2>Summary</h2>
    <?php
    $overallStatus = $allLimitsOk && $vendorExists && $allFilesExist && $allDirsOk && $allExtensionsLoaded;
    
    if ($overallStatus) {
        echo "<div class='check pass'>";
        echo "<span class='icon'>🎉</span>";
        echo "<strong>All Checks PASSED!</strong><br><br>";
        echo "Your system is properly configured. If you're still getting 500 errors:<br>";
        echo "1. Check the error log above for specific errors<br>";
        echo "2. Test form submission with a small amount of data first<br>";
        echo "3. Check browser console for JavaScript errors<br>";
        echo "4. Verify your web server (Apache/Nginx) error logs";
        echo "</div>";
    } else {
        echo "<div class='check fail'>";
        echo "<span class='icon'>⚠️</span>";
        echo "<strong>Issues Found</strong><br><br>";
        echo "Please fix the issues marked with ❌ above, then refresh this page.";
        echo "</div>";
    }
    ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <strong>Quick Actions:</strong><br><br>
        <a href="verify-upload-limits.php" class="fix-btn">📊 Check Upload Limits</a>
        <a href="check-gd-extension.php" class="fix-btn">🖼️ Check GD Extension</a>
        <a href="test-directory-system.php" class="fix-btn">📁 Test Directories</a>
        <a href="verify-paths.php" class="fix-btn">🔍 Verify Paths</a>
        <a href="test-image-upload-fix.php" class="fix-btn">📤 Test Upload</a>
    </div>
</div>
</body>
</html>

<?php
function convertToBytes($value) {
    $value = trim($value);
    $last = strtolower($value[strlen($value)-1]);
    $value = (int)$value;
    
    switch($last) {
        case 'g':
            $value *= 1024;
        case 'm':
            $value *= 1024;
        case 'k':
            $value *= 1024;
    }
    
    return $value;
}
?>
