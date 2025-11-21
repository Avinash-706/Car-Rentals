<?php
/**
 * GD Extension Diagnostic Tool
 * Checks if GD extension is properly installed and configured
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>GD Extension Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #2196F3; }
        .status { padding: 15px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .check-icon { font-size: 20px; margin-right: 10px; }
        .yes { color: #28a745; }
        .no { color: #dc3545; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 GD Extension Diagnostic Tool</h1>
    
    <?php
    $allGood = true;
    
    // Check if GD is loaded
    echo "<h2>1. GD Extension Status</h2>";
    if (extension_loaded('gd')) {
        echo "<div class='status success'><span class='check-icon'>✅</span><strong>GD Extension is LOADED</strong></div>";
    } else {
        echo "<div class='status error'><span class='check-icon'>❌</span><strong>GD Extension is NOT LOADED</strong></div>";
        echo "<div class='status warning'>";
        echo "<strong>How to fix:</strong><br>";
        echo "• <strong>Windows (XAMPP/WAMP):</strong> Edit <code>php.ini</code> and uncomment <code>;extension=gd</code> (remove the semicolon)<br>";
        echo "• <strong>Linux (Ubuntu/Debian):</strong> Run <code>sudo apt-get install php-gd</code> then restart Apache<br>";
        echo "• <strong>macOS (Homebrew):</strong> Run <code>brew install php-gd</code><br>";
        echo "• After installation, restart your web server";
        echo "</div>";
        $allGood = false;
    }
    
    // Check GD version
    if (extension_loaded('gd')) {
        echo "<h2>2. GD Version Information</h2>";
        $gdInfo = gd_info();
        echo "<table>";
        echo "<tr><th>Property</th><th>Value</th></tr>";
        foreach ($gdInfo as $key => $value) {
            $displayValue = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
            echo "<tr><td>$key</td><td>$displayValue</td></tr>";
        }
        echo "</table>";
    }
    
    // Check required functions
    echo "<h2>3. Required GD Functions</h2>";
    $requiredFunctions = [
        'imagecreatefromjpeg' => 'Create image from JPEG file',
        'imagecreatefrompng' => 'Create image from PNG file',
        'imagecreatefromgif' => 'Create image from GIF file',
        'imagecreatetruecolor' => 'Create true color image',
        'imagecopyresampled' => 'Copy and resize image',
        'imagejpeg' => 'Output JPEG image',
        'imagepng' => 'Output PNG image',
        'imagedestroy' => 'Destroy image resource',
        'getimagesize' => 'Get image dimensions',
        'imagecolorallocate' => 'Allocate color',
        'imagefill' => 'Fill image with color',
        'imagealphablending' => 'Set alpha blending',
        'imagesavealpha' => 'Save alpha channel'
    ];
    
    echo "<table>";
    echo "<tr><th>Function</th><th>Description</th><th>Status</th></tr>";
    
    foreach ($requiredFunctions as $func => $desc) {
        $exists = function_exists($func);
        $icon = $exists ? "<span class='yes'>✅</span>" : "<span class='no'>❌</span>";
        $status = $exists ? "Available" : "Missing";
        echo "<tr><td><code>$func</code></td><td>$desc</td><td>$icon $status</td></tr>";
        
        if (!$exists) {
            $allGood = false;
        }
    }
    echo "</table>";
    
    // Check image format support
    if (extension_loaded('gd')) {
        echo "<h2>4. Supported Image Formats</h2>";
        $formats = [
            'JPEG' => imagetypes() & IMG_JPG,
            'PNG' => imagetypes() & IMG_PNG,
            'GIF' => imagetypes() & IMG_GIF,
            'WebP' => imagetypes() & IMG_WEBP,
            'BMP' => imagetypes() & IMG_BMP
        ];
        
        echo "<table>";
        echo "<tr><th>Format</th><th>Supported</th></tr>";
        foreach ($formats as $format => $supported) {
            $icon = $supported ? "<span class='yes'>✅</span>" : "<span class='no'>❌</span>";
            $status = $supported ? "Yes" : "No";
            echo "<tr><td>$format</td><td>$icon $status</td></tr>";
        }
        echo "</table>";
    }
    
    // Check file permissions
    echo "<h2>5. Directory Permissions</h2>";
    $directories = [
        'uploads' => __DIR__ . '/uploads',
        'uploads/drafts' => __DIR__ . '/uploads/drafts',
        'uploads/compressed' => __DIR__ . '/uploads/compressed',
        'uploads/uniform' => __DIR__ . '/uploads/uniform',
        'tmp' => __DIR__ . '/tmp'
    ];
    
    echo "<table>";
    echo "<tr><th>Directory</th><th>Exists</th><th>Writable</th></tr>";
    
    foreach ($directories as $name => $path) {
        $exists = file_exists($path);
        $writable = $exists && is_writable($path);
        
        $existsIcon = $exists ? "<span class='yes'>✅</span>" : "<span class='no'>❌</span>";
        $writableIcon = $writable ? "<span class='yes'>✅</span>" : "<span class='no'>❌</span>";
        
        echo "<tr><td><code>$name</code></td><td>$existsIcon</td><td>$writableIcon</td></tr>";
        
        if (!$exists || !$writable) {
            $allGood = false;
        }
    }
    echo "</table>";
    
    // Test image creation
    if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
        echo "<h2>6. Image Creation Test</h2>";
        try {
            $testImage = imagecreatetruecolor(100, 100);
            if ($testImage) {
                $white = imagecolorallocate($testImage, 255, 255, 255);
                imagefill($testImage, 0, 0, $white);
                imagedestroy($testImage);
                echo "<div class='status success'><span class='check-icon'>✅</span><strong>Image creation test PASSED</strong></div>";
            } else {
                echo "<div class='status error'><span class='check-icon'>❌</span><strong>Image creation test FAILED</strong></div>";
                $allGood = false;
            }
        } catch (Exception $e) {
            echo "<div class='status error'><span class='check-icon'>❌</span><strong>Image creation test FAILED:</strong> " . $e->getMessage() . "</div>";
            $allGood = false;
        }
    }
    
    // PHP Info
    echo "<h2>7. PHP Configuration</h2>";
    echo "<table>";
    echo "<tr><th>Setting</th><th>Value</th></tr>";
    echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
    echo "<tr><td>Memory Limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
    echo "<tr><td>Upload Max Filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
    echo "<tr><td>Post Max Size</td><td>" . ini_get('post_max_size') . "</td></tr>";
    echo "<tr><td>Max Execution Time</td><td>" . ini_get('max_execution_time') . " seconds</td></tr>";
    echo "</table>";
    
    // Final verdict
    echo "<h2>Final Verdict</h2>";
    if ($allGood) {
        echo "<div class='status success'>";
        echo "<span class='check-icon'>✅</span>";
        echo "<strong>All checks PASSED!</strong><br>";
        echo "Your system is ready for image upload and processing.";
        echo "</div>";
    } else {
        echo "<div class='status error'>";
        echo "<span class='check-icon'>❌</span>";
        echo "<strong>Some checks FAILED!</strong><br>";
        echo "Please fix the issues above before using the image upload system.";
        echo "</div>";
    }
    ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <strong>Next Steps:</strong>
        <ol>
            <li>If GD extension is missing, install it using the instructions above</li>
            <li>Restart your web server after installing GD</li>
            <li>Refresh this page to verify the installation</li>
            <li>Test image upload in your application</li>
        </ol>
    </div>
</div>
</body>
</html>
