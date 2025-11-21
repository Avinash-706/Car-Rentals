<?php
/**
 * Debug Draft Paths
 * Shows all draft files and their image paths
 */

require_once 'auto-config.php';
require_once 'init-directories.php';
define('APP_INIT', true);
require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><title>Draft Paths Debug</title>";
echo "<style>
body { font-family: monospace; padding: 20px; background: #f5f5f5; }
.draft { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.draft h2 { margin: 0 0 15px 0; color: #2196F3; }
.file { background: #e3f2fd; padding: 10px; margin: 5px 0; border-radius: 4px; }
.exists { color: green; }
.missing { color: red; }
.path { color: #666; font-size: 12px; }
img { max-width: 150px; margin: 5px; border: 2px solid #ddd; }
.error { background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; }
.success { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 4px; }
</style></head><body>";

echo "<h1>🔍 Draft Paths Debugger</h1>";

try {
    $draftDir = DirectoryManager::getAbsolutePath('uploads/drafts') . DIRECTORY_SEPARATOR;
    
    if (!is_dir($draftDir)) {
        echo "<div class='error'>❌ Drafts directory not found: $draftDir</div>";
        exit;
    }
    
    $draftFiles = glob($draftDir . '*.json');
    
    if (empty($draftFiles)) {
        echo "<div class='error'>❌ No draft files found in: $draftDir</div>";
        exit;
    }
    
    echo "<div class='success'>✅ Found " . count($draftFiles) . " draft files</div>";
    
    foreach ($draftFiles as $draftFile) {
        $draftId = basename($draftFile, '.json');
        $draftData = json_decode(file_get_contents($draftFile), true);
        
        if (!$draftData) {
            echo "<div class='draft'>";
            echo "<h2>Draft: $draftId</h2>";
            echo "<div class='error'>❌ Invalid JSON</div>";
            echo "</div>";
            continue;
        }
        
        echo "<div class='draft'>";
        echo "<h2>Draft: $draftId</h2>";
        echo "<p><strong>Current Step:</strong> " . ($draftData['current_step'] ?? 'N/A') . "</p>";
        echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s', $draftData['timestamp'] ?? 0) . "</p>";
        
        if (isset($draftData['uploaded_files']) && !empty($draftData['uploaded_files'])) {
            echo "<h3>Uploaded Files (" . count($draftData['uploaded_files']) . "):</h3>";
            
            foreach ($draftData['uploaded_files'] as $fieldName => $filePath) {
                echo "<div class='file'>";
                echo "<strong>$fieldName:</strong><br>";
                echo "<div class='path'>Stored Path: $filePath</div>";
                
                // Try different path interpretations
                $tests = [
                    'As-is' => $filePath,
                    'Absolute' => DirectoryManager::getAbsolutePath($filePath),
                    'From root' => __DIR__ . '/' . $filePath,
                ];
                
                $found = false;
                foreach ($tests as $testName => $testPath) {
                    if (file_exists($testPath)) {
                        echo "<div class='exists'>✅ $testName: $testPath</div>";
                        
                        // Try to display image
                        $webPath = DirectoryManager::toWebPath(DirectoryManager::getRelativePath($testPath));
                        echo "<div>Web Path: $webPath</div>";
                        echo "<img src='$webPath' alt='$fieldName' onerror='this.style.border=\"2px solid red\"' onload='this.style.border=\"2px solid green\"'>";
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    echo "<div class='missing'>❌ File not found in any location</div>";
                    foreach ($tests as $testName => $testPath) {
                        echo "<div class='path'>Tried $testName: $testPath</div>";
                    }
                }
                
                echo "</div>";
            }
        } else {
            echo "<div class='error'>❌ No uploaded files in this draft</div>";
        }
        
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
