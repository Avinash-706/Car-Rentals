<?php
/**
 * View All Saved Drafts
 * Shows all draft data in a readable format
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Draft Viewer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #2196F3; }
        .draft-card { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .draft-header { border-bottom: 2px solid #2196F3; padding-bottom: 10px; margin-bottom: 15px; }
        .draft-id { font-size: 18px; font-weight: bold; color: #333; }
        .draft-meta { color: #666; font-size: 14px; margin: 5px 0; }
        .section { margin: 15px 0; }
        .section-title { font-weight: bold; color: #2196F3; margin-bottom: 10px; }
        .field { margin: 5px 0; padding: 5px; background: #f9f9f9; border-left: 3px solid #2196F3; }
        .field-name { font-weight: bold; color: #555; }
        .field-value { color: #000; }
        .image-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
        .image-item { background: #f0f0f0; padding: 10px; border-radius: 4px; font-size: 12px; word-break: break-all; }
        .stats { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .stat-item { display: inline-block; margin: 0 20px; }
        .stat-label { font-weight: bold; color: #1976d2; }
        .no-drafts { text-align: center; padding: 40px; color: #999; }
        .btn { background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #1976d2; }
        .btn-danger { background: #f44336; }
        .btn-danger:hover { background: #d32f2f; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>📦 Draft Viewer</h1>";

// Get all draft JSON files
$draftFiles = glob('uploads/drafts/draft_*.json');

if (empty($draftFiles)) {
    echo "<div class='no-drafts'>
        <h2>No drafts found</h2>
        <p>There are currently no saved drafts in the system.</p>
    </div>";
} else {
    // Statistics
    $totalDrafts = count($draftFiles);
    $totalImages = 0;
    $totalSize = 0;
    
    foreach ($draftFiles as $file) {
        $totalSize += filesize($file);
        $data = json_decode(file_get_contents($file), true);
        if (isset($data['uploaded_files'])) {
            $totalImages += count($data['uploaded_files']);
        }
    }
    
    echo "<div class='stats'>
        <div class='stat-item'>
            <span class='stat-label'>Total Drafts:</span> {$totalDrafts}
        </div>
        <div class='stat-item'>
            <span class='stat-label'>Total Images:</span> {$totalImages}
        </div>
        <div class='stat-item'>
            <span class='stat-label'>Total Size:</span> " . formatBytes($totalSize) . "
        </div>
    </div>";
    
    echo "<a href='?action=cleanup' class='btn btn-danger' onclick='return confirm(\"Delete all drafts older than 30 days?\")'>🗑️ Cleanup Old Drafts</a>";
    echo "<a href='?' class='btn'>🔄 Refresh</a>";
    
    // Handle cleanup action
    if (isset($_GET['action']) && $_GET['action'] === 'cleanup') {
        $deleted = cleanupOldDrafts();
        echo "<div style='background: #4CAF50; color: white; padding: 15px; border-radius: 4px; margin: 20px 0;'>
            ✅ Deleted {$deleted} old drafts
        </div>";
    }
    
    // Display each draft
    foreach ($draftFiles as $draftFile) {
        $data = json_decode(file_get_contents($draftFile), true);
        
        if (!$data) {
            echo "<div class='draft-card'>
                <p style='color: red;'>❌ Error reading draft: {$draftFile}</p>
            </div>";
            continue;
        }
        
        $draftId = $data['draft_id'] ?? 'Unknown';
        $timestamp = $data['timestamp'] ?? 0;
        $currentStep = $data['current_step'] ?? 0;
        $formData = $data['form_data'] ?? [];
        $uploadedFiles = $data['uploaded_files'] ?? [];
        
        $age = time() - $timestamp;
        $ageText = formatAge($age);
        
        echo "<div class='draft-card'>";
        echo "<div class='draft-header'>";
        echo "<div class='draft-id'>📄 {$draftId}</div>";
        echo "<div class='draft-meta'>📅 Created: " . date('Y-m-d H:i:s', $timestamp) . " ({$ageText} ago)</div>";
        echo "<div class='draft-meta'>📊 Current Step: {$currentStep} / 23</div>";
        echo "<div class='draft-meta'>📝 Form Fields: " . count($formData) . "</div>";
        echo "<div class='draft-meta'>🖼️ Images: " . count($uploadedFiles) . "</div>";
        echo "</div>";
        
        // Form Data Section
        if (!empty($formData)) {
            echo "<div class='section'>";
            echo "<div class='section-title'>📝 Form Data (First 10 fields):</div>";
            $count = 0;
            foreach ($formData as $key => $value) {
                if ($count >= 10) break;
                
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                
                $value = htmlspecialchars(substr($value, 0, 100));
                if (strlen($value) > 100) $value .= '...';
                
                echo "<div class='field'>";
                echo "<span class='field-name'>{$key}:</span> ";
                echo "<span class='field-value'>{$value}</span>";
                echo "</div>";
                
                $count++;
            }
            if (count($formData) > 10) {
                echo "<p style='color: #666; font-style: italic;'>... and " . (count($formData) - 10) . " more fields</p>";
            }
            echo "</div>";
        }
        
        // Uploaded Files Section
        if (!empty($uploadedFiles)) {
            echo "<div class='section'>";
            echo "<div class='section-title'>🖼️ Uploaded Images:</div>";
            echo "<div class='image-list'>";
            foreach ($uploadedFiles as $fieldName => $filePath) {
                $exists = file_exists($filePath);
                $status = $exists ? '✅' : '❌';
                $size = $exists ? formatBytes(filesize($filePath)) : 'Missing';
                
                echo "<div class='image-item'>";
                echo "<strong>{$status} {$fieldName}</strong><br>";
                echo "<small>{$filePath}</small><br>";
                echo "<small>Size: {$size}</small>";
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
        }
        
        echo "<div style='margin-top: 15px;'>";
        echo "<a href='?view={$draftId}' class='btn'>👁️ View Full Data</a>";
        echo "<a href='?delete={$draftId}' class='btn btn-danger' onclick='return confirm(\"Delete this draft?\")'>🗑️ Delete</a>";
        echo "</div>";
        
        echo "</div>";
    }
}

// Handle view full data
if (isset($_GET['view'])) {
    $draftId = $_GET['view'];
    $draftFile = 'uploads/drafts/' . $draftId . '.json';
    
    if (file_exists($draftFile)) {
        $data = json_decode(file_get_contents($draftFile), true);
        echo "<div class='draft-card'>";
        echo "<h2>Full Draft Data: {$draftId}</h2>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 4px; overflow: auto;'>";
        echo htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT));
        echo "</pre>";
        echo "<a href='?' class='btn'>← Back</a>";
        echo "</div>";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $draftId = $_GET['delete'];
    $draftFile = 'uploads/drafts/' . $draftId . '.json';
    
    if (file_exists($draftFile)) {
        $data = json_decode(file_get_contents($draftFile), true);
        
        // Delete images
        if (isset($data['uploaded_files'])) {
            foreach ($data['uploaded_files'] as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                // Delete thumbnail
                $thumbPath = str_replace('uploads/drafts/', 'uploads/drafts/thumb_', $filePath);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }
        }
        
        // Delete JSON file
        unlink($draftFile);
        
        echo "<div style='background: #4CAF50; color: white; padding: 15px; border-radius: 4px; margin: 20px 0;'>
            ✅ Draft deleted successfully
        </div>";
        echo "<meta http-equiv='refresh' content='2;url=view-drafts.php'>";
    }
}

echo "</div></body></html>";

// Helper functions
function formatBytes($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

function formatAge($seconds) {
    if ($seconds < 60) {
        return $seconds . ' seconds';
    } elseif ($seconds < 3600) {
        return floor($seconds / 60) . ' minutes';
    } elseif ($seconds < 86400) {
        return floor($seconds / 3600) . ' hours';
    } else {
        return floor($seconds / 86400) . ' days';
    }
}

function cleanupOldDrafts() {
    $drafts = glob('uploads/drafts/draft_*.json');
    $now = time();
    $maxAge = 30 * 24 * 60 * 60; // 30 days
    $deleted = 0;
    
    foreach ($drafts as $draftFile) {
        $data = json_decode(file_get_contents($draftFile), true);
        $age = $now - ($data['timestamp'] ?? 0);
        
        if ($age > $maxAge) {
            // Delete images
            if (isset($data['uploaded_files'])) {
                foreach ($data['uploaded_files'] as $imagePath) {
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
            
            // Delete JSON
            unlink($draftFile);
            $deleted++;
        }
    }
    
    return $deleted;
}
?>
