<?php
/**
 * Save Draft Handler with Image Upload Support
 * Saves all form data including uploaded images
 */

// Auto-configure PHP settings
require_once 'auto-config.php';

// Force high limits for draft saving with many images
@ini_set('memory_limit', '2048M');
@ini_set('max_execution_time', '600');
@ini_set('upload_max_filesize', '200M');
@ini_set('post_max_size', '500M');
@ini_set('max_file_uploads', '500');
@ini_set('max_input_vars', '5000');

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

$response = ['success' => false, 'message' => '', 'draft_id' => ''];

try {
    // Create drafts directory if it doesn't exist
    $draftDir = 'uploads/drafts/';
    if (!file_exists($draftDir)) {
        mkdir($draftDir, 0755, true);
    }
    
    // Generate unique draft ID (or use existing one)
    $draftId = $_POST['draft_id'] ?? uniqid('draft_', true);
    
    // Prepare draft data
    $draftData = [
        'draft_id' => $draftId,
        'timestamp' => time(),
        'current_step' => $_POST['current_step'] ?? 1,
        'form_data' => [],
        'uploaded_files' => []
    ];
    
    // Save all form fields
    foreach ($_POST as $key => $value) {
        if ($key !== 'draft_id' && $key !== 'current_step') {
            $draftData['form_data'][$key] = $value;
        }
    }
    
    // Load existing draft if it exists to preserve uploaded_files
    $draftFile = $draftDir . $draftId . '.json';
    if (file_exists($draftFile)) {
        $existingDraft = json_decode(file_get_contents($draftFile), true);
        if ($existingDraft && isset($existingDraft['uploaded_files'])) {
            $draftData['uploaded_files'] = $existingDraft['uploaded_files'];
        }
    }
    
    // Get uploaded files from localStorage/uploadedFiles (sent via POST)
    // These were already uploaded via upload-image.php
    if (isset($_POST['uploaded_files_json'])) {
        $uploadedFilesFromClient = json_decode($_POST['uploaded_files_json'], true);
        if (is_array($uploadedFilesFromClient)) {
            $draftData['uploaded_files'] = array_merge(
                $draftData['uploaded_files'],
                $uploadedFilesFromClient
            );
        }
    }
    
    // Also check for existing_* fields (backward compatibility)
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'existing_') === 0 && !empty($value)) {
            $fieldName = str_replace('existing_', '', $key);
            if (!isset($draftData['uploaded_files'][$fieldName])) {
                $draftData['uploaded_files'][$fieldName] = $value;
            }
        }
    }
    
    // Handle any new file uploads (fallback, but should use upload-image.php instead)
    // REMOVED 20-FILE LIMIT - Now processes ALL uploaded files dynamically
    if (!empty($_FILES)) {
        foreach ($_FILES as $fieldName => $file) {
            // Handle both single file and array of files
            if (is_array($file['error'])) {
                // Multiple files in array format
                $fileCount = count($file['error']);
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($file['error'][$i] === UPLOAD_ERR_OK) {
                        $extension = strtolower(pathinfo($file['name'][$i], PATHINFO_EXTENSION));
                        $uniqueName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', basename($file['name'][$i]));
                        $targetPath = $draftDir . $uniqueName;
                        
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) && $file['size'][$i] <= 20 * 1024 * 1024) {
                            if (move_uploaded_file($file['tmp_name'][$i], $targetPath)) {
                                $actualFieldName = $fieldName . '_' . $i;
                                $draftData['uploaded_files'][$actualFieldName] = $targetPath;
                            }
                        }
                    }
                }
            } else {
                // Single file
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $uniqueName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', basename($file['name']));
                    $targetPath = $draftDir . $uniqueName;
                    
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) && $file['size'] <= 20 * 1024 * 1024) {
                        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                            $draftData['uploaded_files'][$fieldName] = $targetPath;
                        }
                    }
                }
            }
        }
    }
    
    // Save draft to JSON file
    $draftFile = $draftDir . $draftId . '.json';
    file_put_contents($draftFile, json_encode($draftData, JSON_PRETTY_PRINT));
    
    $response['success'] = true;
    $response['message'] = 'Draft saved successfully!';
    $response['draft_id'] = $draftId;
    $response['files_saved'] = count($draftData['uploaded_files']);
    $response['draft_data'] = $draftData;
    
} catch (Exception $e) {
    $response['message'] = 'Error saving draft: ' . $e->getMessage();
    if (function_exists('logError')) {
        logError('Draft save error: ' . $e->getMessage(), $_POST);
    }
}

// Ensure clean JSON output
ob_clean();
echo json_encode($response);
exit;
