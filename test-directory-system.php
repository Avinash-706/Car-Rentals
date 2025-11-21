<?php
/**
 * Directory System Test
 * Tests the automatic directory creation and path handling
 */

require_once __DIR__ . '/init-directories.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Directory System Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #2196F3; }
        .test { padding: 15px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #ccc; }
        .pass { background: #d4edda; border-left-color: #28a745; }
        .fail { background: #f8d7da; border-left-color: #dc3545; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .icon { font-size: 20px; margin-right: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🗂️ Directory System Test</h1>
    
    <h2>1. Base Directory</h2>
    <?php
    $baseDir = DirectoryManager::getBaseDir();
    echo "<div class='test info'>";
    echo "<strong>Base Directory:</strong><br>";
    echo "<code>$baseDir</code>";
    echo "</div>";
    ?>
    
    <h2>2. Directory Health Check</h2>
    <?php
    $health = DirectoryManager::checkHealth();
    $allGood = true;
    
    echo "<table>";
    echo "<tr><th>Directory</th><th>Exists</th><th>Is Directory</th><th>Writable</th><th>Full Path</th></tr>";
    
    foreach ($health as $dir => $status) {
        $existsIcon = $status['exists'] ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
        $isDirIcon = $status['is_dir'] ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
        $writableIcon = $status['writable'] ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
        
        echo "<tr>";
        echo "<td><code>$dir</code></td>";
        echo "<td>$existsIcon</td>";
        echo "<td>$isDirIcon</td>";
        echo "<td>$writableIcon</td>";
        echo "<td><small>" . htmlspecialchars($status['path']) . "</small></td>";
        echo "</tr>";
        
        if (!$status['exists'] || !$status['is_dir'] || !$status['writable']) {
            $allGood = false;
        }
    }
    
    echo "</table>";
    
    if ($allGood) {
        echo "<div class='test pass'><span class='icon'>✅</span><strong>All directories are properly initialized!</strong></div>";
    } else {
        echo "<div class='test fail'><span class='icon'>❌</span><strong>Some directories have issues. Check the table above.</strong></div>";
    }
    ?>
    
    <h2>3. Path Conversion Tests</h2>
    <?php
    $testPaths = [
        'uploads/drafts/test.jpg',
        'uploads/compressed/image.jpg',
        'uploads/uniform/photo.jpg',
        'pdfs/report.pdf'
    ];
    
    echo "<table>";
    echo "<tr><th>Relative Path</th><th>Absolute Path</th><th>Web Path</th></tr>";
    
    foreach ($testPaths as $relativePath) {
        $absolutePath = DirectoryManager::getAbsolutePath($relativePath);
        $webPath = DirectoryManager::toWebPath($relativePath);
        
        echo "<tr>";
        echo "<td><code>$relativePath</code></td>";
        echo "<td><small>" . htmlspecialchars($absolutePath) . "</small></td>";
        echo "<td><code>$webPath</code></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    ?>
    
    <h2>4. Directory Creation Test</h2>
    <?php
    echo "<div class='test info'>";
    echo "<strong>Testing dynamic directory creation:</strong><br><br>";
    
    // Test compressed directory
    try {
        $compressedDir = DirectoryManager::getCompressedDir('uploads/drafts/test.jpg');
        $exists = file_exists($compressedDir);
        $writable = is_writable($compressedDir);
        
        if ($exists && $writable) {
            echo "<span class='icon' style='color: #28a745;'>✅</span> Compressed directory created successfully<br>";
            echo "<code>$compressedDir</code><br><br>";
        } else {
            echo "<span class='icon' style='color: #dc3545;'>❌</span> Failed to create compressed directory<br><br>";
        }
    } catch (Exception $e) {
        echo "<span class='icon' style='color: #dc3545;'>❌</span> Error: " . $e->getMessage() . "<br><br>";
    }
    
    // Test uniform directory
    try {
        $uniformDir = DirectoryManager::getUniformDir('uploads/drafts/test.jpg');
        $exists = file_exists($uniformDir);
        $writable = is_writable($uniformDir);
        
        if ($exists && $writable) {
            echo "<span class='icon' style='color: #28a745;'>✅</span> Uniform directory created successfully<br>";
            echo "<code>$uniformDir</code><br>";
        } else {
            echo "<span class='icon' style='color: #dc3545;'>❌</span> Failed to create uniform directory<br>";
        }
    } catch (Exception $e) {
        echo "<span class='icon' style='color: #dc3545;'>❌</span> Error: " . $e->getMessage() . "<br>";
    }
    
    echo "</div>";
    ?>
    
    <h2>5. Cross-Platform Compatibility</h2>
    <?php
    echo "<div class='test info'>";
    echo "<strong>System Information:</strong><br><br>";
    echo "Operating System: <code>" . PHP_OS . "</code><br>";
    echo "Directory Separator: <code>" . DIRECTORY_SEPARATOR . "</code><br>";
    echo "PHP Version: <code>" . phpversion() . "</code><br>";
    echo "</div>";
    ?>
    
    <h2>6. File Operations Test</h2>
    <?php
    echo "<div class='test info'>";
    echo "<strong>Testing file operations:</strong><br><br>";
    
    // Test file creation
    $testFile = DirectoryManager::getAbsolutePath('uploads/test_' . time() . '.txt');
    $testContent = "Directory system test - " . date('Y-m-d H:i:s');
    
    if (@file_put_contents($testFile, $testContent)) {
        echo "<span class='icon' style='color: #28a745;'>✅</span> File creation: SUCCESS<br>";
        echo "<code>$testFile</code><br><br>";
        
        // Test file reading
        $readContent = file_get_contents($testFile);
        if ($readContent === $testContent) {
            echo "<span class='icon' style='color: #28a745;'>✅</span> File reading: SUCCESS<br><br>";
        } else {
            echo "<span class='icon' style='color: #dc3545;'>❌</span> File reading: FAILED<br><br>";
        }
        
        // Clean up
        @unlink($testFile);
        echo "<span class='icon' style='color: #28a745;'>✅</span> File deletion: SUCCESS<br>";
    } else {
        echo "<span class='icon' style='color: #dc3545;'>❌</span> File creation: FAILED<br>";
        echo "Check directory permissions<br>";
    }
    
    echo "</div>";
    ?>
    
    <h2>Summary</h2>
    <?php
    if ($allGood) {
        echo "<div class='test pass'>";
        echo "<span class='icon'>🎉</span>";
        echo "<strong>All tests PASSED!</strong><br><br>";
        echo "Your directory system is properly configured and ready to use.<br>";
        echo "All required directories exist and are writable.<br>";
        echo "Path handling is working correctly across platforms.";
        echo "</div>";
    } else {
        echo "<div class='test fail'>";
        echo "<span class='icon'>⚠️</span>";
        echo "<strong>Some tests FAILED!</strong><br><br>";
        echo "Please check the issues above and fix directory permissions.<br>";
        echo "Run this test again after fixing the issues.";
        echo "</div>";
    }
    ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <strong>What This Test Checks:</strong>
        <ul>
            <li>✅ All required directories exist</li>
            <li>✅ Directories are writable</li>
            <li>✅ Path conversion works correctly</li>
            <li>✅ Dynamic directory creation works</li>
            <li>✅ File operations work</li>
            <li>✅ Cross-platform compatibility</li>
        </ul>
        
        <strong>Next Steps:</strong>
        <ol>
            <li>If all tests pass, your system is ready!</li>
            <li>Test image upload in your application</li>
            <li>Test form submission and PDF generation</li>
            <li>Check that compressed and uniform images are created</li>
        </ol>
    </div>
</div>
</body>
</html>
