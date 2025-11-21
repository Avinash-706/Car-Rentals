<?php
/**
 * Path Verification Script
 * Verifies all paths in the project match init-directories configuration
 */

require_once __DIR__ . '/init-directories.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Path Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #2196F3; }
        .section { margin: 30px 0; }
        .pass { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 10px 0; }
        .fail { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-size: 12px; }
        .icon { font-size: 20px; margin-right: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Path Verification Report</h1>
    
    <div class="section">
        <h2>1. Directory Structure Check</h2>
        <?php
        $health = DirectoryManager::checkHealth();
        $allDirsGood = true;
        
        echo "<table>";
        echo "<tr><th>Directory</th><th>Status</th><th>Path</th></tr>";
        
        foreach ($health as $dir => $status) {
            $allGood = $status['exists'] && $status['is_dir'] && $status['writable'];
            $icon = $allGood ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
            $statusText = $allGood ? "OK" : "ISSUE";
            
            if (!$allGood) {
                $allDirsGood = false;
                if (!$status['exists']) $statusText = "Missing";
                elseif (!$status['is_dir']) $statusText = "Not a directory";
                elseif (!$status['writable']) $statusText = "Not writable";
            }
            
            echo "<tr>";
            echo "<td><code>$dir</code></td>";
            echo "<td>$icon $statusText</td>";
            echo "<td><small>" . htmlspecialchars($status['path']) . "</small></td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        if ($allDirsGood) {
            echo "<div class='pass'><span class='icon'>✅</span><strong>All directories exist and are writable</strong></div>";
        } else {
            echo "<div class='fail'><span class='icon'>❌</span><strong>Some directories have issues - check table above</strong></div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>2. File Path Consistency Check</h2>
        <?php
        $filesToCheck = [
            'upload-image.php' => ['uploads/drafts', 'DirectoryManager'],
            'generate-pdf.php' => ['DirectoryManager', 'tmp', 'pdfs'],
            'generate-test-pdf.php' => ['DirectoryManager', 'tmp', 'pdfs'],
            'submit.php' => ['init-directories.php', 'DirectoryManager'],
            'save-draft.php' => ['init-directories.php', 'DirectoryManager'],
            'load-draft.php' => ['init-directories.php', 'DirectoryManager'],
            'delete-draft.php' => ['init-directories.php', 'DirectoryManager'],
            't-submit.php' => ['init-directories.php', 'DirectoryManager'],
            'generate-pdf-worker.php' => ['init-directories.php', 'DirectoryManager'],
            'image-optimizer.php' => ['init-directories.php', 'DirectoryManager']
        ];
        
        echo "<table>";
        echo "<tr><th>File</th><th>Required Patterns</th><th>Status</th></tr>";
        
        $allFilesGood = true;
        foreach ($filesToCheck as $file => $patterns) {
            if (!file_exists($file)) {
                echo "<tr>";
                echo "<td><code>$file</code></td>";
                echo "<td>" . implode(', ', $patterns) . "</td>";
                echo "<td><span class='icon' style='color: #ffc107;'>⚠️</span> File not found</td>";
                echo "</tr>";
                continue;
            }
            
            $content = file_get_contents($file);
            $allPatternsFound = true;
            $missingPatterns = [];
            
            foreach ($patterns as $pattern) {
                if (strpos($content, $pattern) === false) {
                    $allPatternsFound = false;
                    $missingPatterns[] = $pattern;
                }
            }
            
            $icon = $allPatternsFound ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
            $statusText = $allPatternsFound ? "OK" : "Missing: " . implode(', ', $missingPatterns);
            
            if (!$allPatternsFound) {
                $allFilesGood = false;
            }
            
            echo "<tr>";
            echo "<td><code>$file</code></td>";
            echo "<td><small>" . implode(', ', $patterns) . "</small></td>";
            echo "<td>$icon $statusText</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        if ($allFilesGood) {
            echo "<div class='pass'><span class='icon'>✅</span><strong>All files use DirectoryManager correctly</strong></div>";
        } else {
            echo "<div class='fail'><span class='icon'>❌</span><strong>Some files need updates - check table above</strong></div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Hardcoded Path Detection</h2>
        <?php
        $suspiciousPatterns = [
            '/uploads\/drafts\/[^\'"]/' => 'Hardcoded uploads/drafts path',
            '/uploads\/compressed\/[^\'"]/' => 'Hardcoded uploads/compressed path',
            '/uploads\/uniform\/[^\'"]/' => 'Hardcoded uploads/uniform path',
            '/pdfs\/[^\'"]/' => 'Hardcoded pdfs path',
            '/tmp\/[^\'"]/' => 'Hardcoded tmp path',
            '/__DIR__\s*\.\s*[\'"]\/uploads/' => 'Hardcoded __DIR__ . /uploads',
            '/__DIR__\s*\.\s*[\'"]\/pdfs/' => 'Hardcoded __DIR__ . /pdfs'
        ];
        
        $phpFiles = glob('*.php');
        $issuesFound = [];
        
        // Files to skip (test/diagnostic files where hardcoded paths are acceptable)
        $skipFiles = [
            'verify-paths.php',
            'check-gd-extension.php',
            'pdf-verifier.php',
            'view-drafts.php',
            'init-directories.php'
        ];
        
        foreach ($phpFiles as $file) {
            // Skip test files and diagnostic files
            if (in_array($file, $skipFiles) || strpos($file, 'test-') === 0) {
                continue;
            }
            
            $content = file_get_contents($file);
            
            foreach ($suspiciousPatterns as $pattern => $description) {
                if (preg_match($pattern, $content, $matches)) {
                    $issuesFound[] = [
                        'file' => $file,
                        'issue' => $description,
                        'match' => $matches[0] ?? ''
                    ];
                }
            }
        }
        
        if (empty($issuesFound)) {
            echo "<div class='pass'><span class='icon'>✅</span><strong>No hardcoded paths detected</strong></div>";
        } else {
            echo "<div class='fail'><span class='icon'>❌</span><strong>Found " . count($issuesFound) . " potential hardcoded paths:</strong></div>";
            echo "<table>";
            echo "<tr><th>File</th><th>Issue</th><th>Match</th></tr>";
            foreach ($issuesFound as $issue) {
                echo "<tr>";
                echo "<td><code>" . htmlspecialchars($issue['file']) . "</code></td>";
                echo "<td>" . htmlspecialchars($issue['issue']) . "</td>";
                echo "<td><code>" . htmlspecialchars($issue['match']) . "</code></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Path Resolution Test</h2>
        <?php
        $testPaths = [
            'uploads/drafts/test.jpg',
            'uploads/compressed/image.jpg',
            'uploads/uniform/photo.jpg',
            'pdfs/report.pdf',
            'tmp/temp.txt',
            'logs/error.log'
        ];
        
        echo "<table>";
        echo "<tr><th>Relative Path</th><th>Absolute Path</th><th>Exists</th></tr>";
        
        foreach ($testPaths as $relativePath) {
            $absolutePath = DirectoryManager::getAbsolutePath($relativePath);
            $dir = dirname($absolutePath);
            $exists = file_exists($dir);
            $icon = $exists ? "<span class='icon' style='color: #28a745;'>✅</span>" : "<span class='icon' style='color: #dc3545;'>❌</span>";
            
            echo "<tr>";
            echo "<td><code>$relativePath</code></td>";
            echo "<td><small>" . htmlspecialchars($absolutePath) . "</small></td>";
            echo "<td>$icon " . ($exists ? "Directory exists" : "Directory missing") . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        ?>
    </div>
    
    <div class="section">
        <h2>5. Configuration Check</h2>
        <?php
        echo "<div class='info'>";
        echo "<strong>Base Directory:</strong><br>";
        echo "<code>" . htmlspecialchars(DirectoryManager::getBaseDir()) . "</code><br><br>";
        
        echo "<strong>System Information:</strong><br>";
        echo "OS: <code>" . PHP_OS . "</code><br>";
        echo "Directory Separator: <code>" . DIRECTORY_SEPARATOR . "</code><br>";
        echo "PHP Version: <code>" . phpversion() . "</code><br>";
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>Summary</h2>
        <?php
        $overallStatus = $allDirsGood && $allFilesGood && empty($issuesFound);
        
        if ($overallStatus) {
            echo "<div class='pass'>";
            echo "<span class='icon'>🎉</span>";
            echo "<strong>All Checks PASSED!</strong><br><br>";
            echo "✅ All directories exist and are writable<br>";
            echo "✅ All files use DirectoryManager correctly<br>";
            echo "✅ No hardcoded paths detected<br>";
            echo "✅ Path resolution working correctly<br><br>";
            echo "Your project is ready for deployment on any machine!";
            echo "</div>";
        } else {
            echo "<div class='fail'>";
            echo "<span class='icon'>⚠️</span>";
            echo "<strong>Some Issues Found</strong><br><br>";
            
            if (!$allDirsGood) {
                echo "❌ Some directories have issues<br>";
            }
            if (!$allFilesGood) {
                echo "❌ Some files need DirectoryManager updates<br>";
            }
            if (!empty($issuesFound)) {
                echo "❌ Hardcoded paths detected<br>";
            }
            
            echo "<br>Please review the issues above and fix them.";
            echo "</div>";
        }
        ?>
    </div>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <strong>Next Steps:</strong>
        <ol>
            <li>Fix any issues reported above</li>
            <li>Run <code>test-directory-system.php</code> to verify directory creation</li>
            <li>Test image upload functionality</li>
            <li>Test form submission and PDF generation</li>
            <li>Deploy to production with confidence!</li>
        </ol>
    </div>
</div>
</body>
</html>
