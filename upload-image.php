<?php
/**
 * Async Single Image Upload Handler
 * Uploads, compresses, and saves one image at a time
 */

require_once 'auto-config.php';
// Force high limits for image uploads
@ini_set('memory_limit', '2048M');
@ini_set('max_execution_time', '600');
@ini_set('upload_max_filesize', '200M');
@ini_set('post_max_size', '500M');
@ini_set('max_file_uploads', '500');

define('APP_INIT', true);
require_once 'config.php';
require_once 'image-optimizer.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    if (empty($_FILES['image'])) {
        throw new Exception('No image uploaded');
    }
    
    $file = $_FILES['image'];
    $fieldName = $_POST['field_name'] ?? 'unknown';
    $draftId = $_POST['draft_id'] ?? uniqid('draft_', true);
    $step = $_POST['step'] ?? 'unknown';
    $userId = $_POST['user_id'] ?? 'guest';
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error: ' . $file['error']);
    }
    
    // Validate size (20MB max before compression)
    if ($file['size'] > 20 * 1024 * 1024) {
        throw new Exception('File size exceeds 20MB limit');
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate extension
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, WebP allowed.');
    }
    
    // Validate mime type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
        throw new Exception('Invalid image file');
    }
    
    // Create drafts directory
    $draftDir = 'uploads/drafts/';
    if (!file_exists($draftDir)) {
        mkdir($draftDir, 0755, true);
    }
    
    // Generate unique filename
    $timestamp = time();
    $random = substr(md5(uniqid()), 0, 8);
    $slug = preg_replace('/[^a-zA-Z0-9_.-]/', '', pathinfo($file['name'], PATHINFO_FILENAME));
    $slug = substr($slug, 0, 50); // Limit length
    $uniqueName = "{$timestamp}_{$userId}_{$random}_{$slug}.jpg"; // Always save as JPG
    $targetPath = $draftDir . $uniqueName;
    
    // Move uploaded file temporarily
    $tempPath = $file['tmp_name'];
    
    // Compress and resize image
    $compressedPath = ImageOptimizer::compressToFile($tempPath, 1200, 70);
    
    // If compression failed, use original
    if (!$compressedPath || !file_exists($compressedPath)) {
        if (!move_uploaded_file($tempPath, $targetPath)) {
            throw new Exception('Failed to save uploaded file');
        }
    } else {
        // Move compressed file to target
        rename($compressedPath, $targetPath);
    }
    
    // Get image dimensions and checksum
    $imageInfo = getimagesize($targetPath);
    $checksum = hash_file('sha256', $targetPath);
    
    // Create thumbnail (300px)
    $thumbPath = $draftDir . "thumb_{$uniqueName}";
    ImageOptimizer::compressToFile($targetPath, 300, 70);
    if (file_exists($draftDir . 'compressed/compressed_' . basename($targetPath))) {
        rename($draftDir . 'compressed/compressed_' . basename($targetPath), $thumbPath);
    }
    
    // Update draft JSON
    $draftFile = $draftDir . $draftId . '.json';
    $draftData = [];
    
    if (file_exists($draftFile)) {
        $draftData = json_decode(file_get_contents($draftFile), true) ?: [];
    } else {
        $draftData = [
            'draft_id' => $draftId,
            'version' => 1,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
            'owner_id' => $userId,
            'archived' => false,
            'current_step' => $step,
            'form_data' => [],
            'uploaded_files' => []
        ];
    }
    
    // Add file to uploaded_files
    $draftData['uploaded_files'][$fieldName] = $targetPath;
    $draftData['updated_at'] = $timestamp;
    $draftData['version'] = ($draftData['version'] ?? 0) + 1;
    
    // Save draft
    file_put_contents($draftFile, json_encode($draftData, JSON_PRETTY_PRINT));
    
    // Log to audit trail
    $auditDir = 'drafts/audit';
    if (!file_exists($auditDir)) {
        mkdir($auditDir, 0755, true);
    }
    $auditLog = $auditDir . "/{$draftId}.log";
    $auditEntry = date('Y-m-d H:i:s') . " - Image uploaded: $fieldName -> $targetPath\n";
    file_put_contents($auditLog, $auditEntry, FILE_APPEND);
    
    $response['success'] = true;
    $response['message'] = 'Image uploaded and compressed successfully';
    $response['path'] = $targetPath;
    $response['thumb_path'] = file_exists($thumbPath) ? $thumbPath : $targetPath;
    $response['checksum'] = $checksum;
    $response['size'] = filesize($targetPath);
    $response['width'] = $imageInfo[0] ?? 0;
    $response['height'] = $imageInfo[1] ?? 0;
    $response['draft_id'] = $draftId;
    $response['version'] = $draftData['version'];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log('Image upload error: ' . $e->getMessage());
}

echo json_encode($response);
exit;

