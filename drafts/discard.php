<?php
/**
 * Discard Draft - Complete Cleanup
 * POST /drafts/discard.php
 * Deletes draft JSON and all associated images
 */

require_once __DIR__ . '/../auto-config.php';
define('APP_INIT', true);
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $draftId = $_POST['draft_id'] ?? $_GET['draft_id'] ?? null;
    
    if (!$draftId) {
        throw new Exception('Draft ID required');
    }
    
    $draftFile = __DIR__ . '/../uploads/drafts/' . $draftId . '.json';
    
    if (!file_exists($draftFile)) {
        // Already deleted, return success
        $response['success'] = true;
        $response['message'] = 'Draft already deleted';
        echo json_encode($response);
        exit;
    }
    
    // Load draft to get image paths
    $draftData = json_decode(file_get_contents($draftFile), true);
    
    $deletedImages = 0;
    $deletedFiles = 0;
    
    // Delete all uploaded images
    if (isset($draftData['uploaded_files']) && is_array($draftData['uploaded_files'])) {
        foreach ($draftData['uploaded_files'] as $fieldName => $filePath) {
            if (empty($filePath)) continue;
            
            // Handle both absolute and relative paths
            if (file_exists($filePath)) {
                $absolutePath = $filePath;
            } else {
                // Try as relative path from project root
                $absolutePath = __DIR__ . '/../' . $filePath;
            }
            
            if (file_exists($absolutePath)) {
                if (@unlink($absolutePath)) {
                    $deletedImages++;
                    error_log("Deleted draft image: $absolutePath");
                }
                
                // Also delete thumbnail if exists
                $thumbPath = dirname($absolutePath) . DIRECTORY_SEPARATOR . 'thumb_' . basename($absolutePath);
                if (file_exists($thumbPath)) {
                    @unlink($thumbPath);
                    error_log("Deleted draft thumbnail: $thumbPath");
                }
            } else {
                error_log("Draft image not found for deletion: $filePath (tried: $absolutePath)");
            }
        }
    }
    
    // Delete all version files
    $versionPattern = str_replace('.json', '.v*.json', $draftFile);
    $versionFiles = glob($versionPattern);
    foreach ($versionFiles as $versionFile) {
        if (unlink($versionFile)) {
            $deletedFiles++;
        }
    }
    
    // Delete backup files
    $backupFile = dirname($draftFile) . '/backup_' . basename($draftFile);
    if (file_exists($backupFile)) {
        unlink($backupFile);
        $deletedFiles++;
    }
    
    // Delete audit log
    $auditFile = __DIR__ . '/../drafts/audit/' . $draftId . '.log';
    if (file_exists($auditFile)) {
        unlink($auditFile);
    }
    
    // Delete draft JSON file
    if (unlink($draftFile)) {
        $deletedFiles++;
    }
    
    // Log the discard action
    error_log("Draft discarded: $draftId - Deleted $deletedImages images and $deletedFiles files");
    
    $response['success'] = true;
    $response['message'] = 'Draft discarded successfully';
    $response['deleted_images'] = $deletedImages;
    $response['deleted_files'] = $deletedFiles;
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log('Draft discard error: ' . $e->getMessage());
}

echo json_encode($response);
exit;
