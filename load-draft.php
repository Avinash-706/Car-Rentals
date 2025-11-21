<?php
/**
 * Load Draft Handler
 * Retrieves saved draft including uploaded images
 */

// Auto-configure PHP settings
require_once 'auto-config.php';

// Prevent any output before JSON
ob_start();

// Set error handler to catch all errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

define('APP_INIT', true);
require_once 'config.php';

// Clear any output that might have occurred
ob_end_clean();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'draft_data' => null];

try {
    $draftId = $_GET['draft_id'] ?? '';
    
    if (empty($draftId)) {
        throw new Exception('Draft ID is required');
    }
    
    $draftFile = 'uploads/drafts/' . $draftId . '.json';
    
    if (!file_exists($draftFile)) {
        throw new Exception('Draft not found');
    }
    
    // Load draft data
    $draftData = json_decode(file_get_contents($draftFile), true);
    
    if (!$draftData) {
        throw new Exception('Invalid draft data');
    }
    
    // Verify uploaded files still exist
    if (isset($draftData['uploaded_files'])) {
        foreach ($draftData['uploaded_files'] as $fieldName => $filePath) {
            if (!file_exists($filePath)) {
                // File was deleted, remove from draft
                unset($draftData['uploaded_files'][$fieldName]);
            }
        }
    }
    
    $response['success'] = true;
    $response['message'] = 'Draft loaded successfully';
    $response['draft_data'] = $draftData;
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    if (function_exists('logError')) {
        logError('Draft load error: ' . $e->getMessage(), $_GET);
    }
}

// Ensure clean JSON output
ob_clean();
echo json_encode($response);
exit;
