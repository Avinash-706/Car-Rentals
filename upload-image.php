<?php
/**
 * Async Single Image Upload Handler
 * Uploads, compresses, and saves one image at a time
 * Cross-platform compatible with guaranteed JSON responses
 */

// Prevent any output before JSON
ob_start();

// Error handler to catch all errors and convert to exceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Exception handler to ensure JSON response even on fatal errors
set_exception_handler(function($exception) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $exception->getMessage(),
        'error_type' => get_class($exception),
        'file' => basename($exception->getFile()),
        'line' => $exception->getLine()
    ]);
    exit;
});

try {
    require_once __DIR__ . '/auto-config.php';
    
    // Force high limits for image uploads
    @ini_set('memory_limit', '2048M');
    @ini_set('max_execution_time', '600');
    @ini_set('upload_max_filesize', '200M');
    @ini_set('post_max_size', '500M');
    @ini_set('max_file_uploads', '500');

    define('APP_INIT', true);
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/image-optimizer.php';
    
    // Clear any output buffer
    ob_end_clean();
    
    // Set JSON header
    header('Content-Type: application/json');

    $response = ['success' => false, 'message' => ''];

    // Check if GD extension is available
    if (!extension_loaded('gd')) {
        throw new Exception('GD extension is not installed. Please install php-gd to enable image processing.');
    }
    
    // Check required GD functions
    $requiredFunctions = ['imagecreatefromjpeg', 'imagecreatefrompng', 'imagecreatefromgif', 'imagecreatetruecolor'];
    foreach ($requiredFunctions as $func) {
        if (!function_exists($func)) {
            throw new Exception("Required GD function '$func' is not available. Please check your PHP GD installation.");
        }
    }
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
    
    // Create drafts directory with absolute path
    $baseDir = __DIR__;
    $draftDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'drafts' . DIRECTORY_SEPARATOR;
    
    if (!file_exists($draftDir)) {
        if (!@mkdir($draftDir, 0755, true)) {
            throw new Exception('Failed to create drafts directory: ' . $draftDir);
        }
    }
    
    if (!is_writable($draftDir)) {
        throw new Exception('Drafts directory is not writable: ' . $draftDir);
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
    
    if (!file_exists($tempPath)) {
        throw new Exception('Uploaded file not found in temporary location');
    }
    
    // Try to compress and resize image
    try {
        $compressedPath = ImageOptimizer::compressToFile($tempPath, 1200, 70);
        
        // If compression succeeded, use compressed version
        if ($compressedPath && file_exists($compressedPath) && $compressedPath !== $tempPath) {
            if (!@rename($compressedPath, $targetPath)) {
                // If rename fails, try copy and delete
                if (!@copy($compressedPath, $targetPath)) {
                    throw new Exception('Failed to move compressed file');
                }
                @unlink($compressedPath);
            }
        } else {
            // Compression failed or returned original, move uploaded file
            if (!move_uploaded_file($tempPath, $targetPath)) {
                throw new Exception('Failed to save uploaded file');
            }
        }
    } catch (Exception $e) {
        // If compression fails, fall back to original upload
        error_log('Image compression failed, using original: ' . $e->getMessage());
        if (!move_uploaded_file($tempPath, $targetPath)) {
            throw new Exception('Failed to save uploaded file: ' . $e->getMessage());
        }
    }
    
    // Verify file was saved
    if (!file_exists($targetPath)) {
        throw new Exception('File was not saved successfully');
    }
    
    // Get image dimensions and checksum
    $imageInfo = @getimagesize($targetPath);
    if ($imageInfo === false) {
        error_log('Warning: Could not get image size for: ' . $targetPath);
        $imageInfo = [0, 0]; // Default dimensions
    }
    
    $checksum = hash_file('sha256', $targetPath);
    
    // Create thumbnail (300px) - non-critical, don't fail if it doesn't work
    $thumbPath = $draftDir . "thumb_{$uniqueName}";
    try {
        $thumbCompressed = ImageOptimizer::compressToFile($targetPath, 300, 70);
        if ($thumbCompressed && file_exists($thumbCompressed) && $thumbCompressed !== $targetPath) {
            @rename($thumbCompressed, $thumbPath);
        }
    } catch (Exception $e) {
        error_log('Thumbnail creation failed (non-critical): ' . $e->getMessage());
        // Thumbnail creation is optional, continue without it
    }
    
    // Update draft JSON with absolute path
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
    if (file_put_contents($draftFile, json_encode($draftData, JSON_PRETTY_PRINT)) === false) {
        throw new Exception('Failed to save draft data');
    }
    
    // Log to audit trail with absolute path
    $auditDir = $baseDir . DIRECTORY_SEPARATOR . 'drafts' . DIRECTORY_SEPARATOR . 'audit';
    if (!file_exists($auditDir)) {
        @mkdir($auditDir, 0755, true);
    }
    
    if (is_writable($auditDir)) {
        $auditLog = $auditDir . DIRECTORY_SEPARATOR . "{$draftId}.log";
        $auditEntry = date('Y-m-d H:i:s') . " - Image uploaded: $fieldName -> $targetPath\n";
        @file_put_contents($auditLog, $auditEntry, FILE_APPEND);
    }
    
    // Convert absolute paths to relative for response
    $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $targetPath);
    $relativePath = str_replace('\\', '/', $relativePath); // Normalize for web
    
    $relativeThumbPath = $relativePath;
    if (file_exists($thumbPath)) {
        $relativeThumbPath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $thumbPath);
        $relativeThumbPath = str_replace('\\', '/', $relativeThumbPath);
    }
    
    $response['success'] = true;
    $response['message'] = 'Image uploaded and compressed successfully';
    $response['file_path'] = $relativePath; // For backward compatibility
    $response['path'] = $relativePath;
    $response['thumb_path'] = $relativeThumbPath;
    $response['checksum'] = $checksum;
    $response['size'] = filesize($targetPath);
    $response['width'] = $imageInfo[0] ?? 0;
    $response['height'] = $imageInfo[1] ?? 0;
    $response['draft_id'] = $draftId;
    $response['version'] = $draftData['version'];
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    $response['error_type'] = get_class($e);
    error_log('Image upload error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
} catch (Error $e) {
    $response['success'] = false;
    $response['message'] = 'PHP Error: ' . $e->getMessage();
    $response['error_type'] = get_class($e);
    error_log('Image upload PHP error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
}

// Ensure we always output valid JSON
echo json_encode($response);
exit;

