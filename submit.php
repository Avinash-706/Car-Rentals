<?php
/**
 * Main submission handler - Production Ready
 * Receives form data, validates, saves images, generates PDF, and sends email
 */

// Auto-configure PHP settings
require_once 'auto-config.php';

// Force high memory and time for submission with many images
// CRITICAL: Support for 500+ image uploads
@ini_set('memory_limit', '2048M');
@ini_set('max_execution_time', '600');
@ini_set('max_file_uploads', '500');
@ini_set('post_max_size', '500M');
@ini_set('upload_max_filesize', '200M');
@ini_set('max_input_vars', '5000');
@set_time_limit(600);

define('APP_INIT', true);
require_once 'config.php';

// Prevent any output before JSON
ob_start();

header('Content-Type: application/json');

// Response array
$response = ['success' => false, 'message' => ''];

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Log submission details
    error_log("Form submission received. Files count: " . count($_FILES) . ", POST fields: " . count($_POST));
    
    // DEBUG: Log Step 4 checkbox data
    error_log("Step 4 Checkbox Debug:");
    error_log("  registration_certificate: " . (isset($_POST['registration_certificate']) ? print_r($_POST['registration_certificate'], true) : 'NOT SET'));
    error_log("  car_insurance: " . (isset($_POST['car_insurance']) ? print_r($_POST['car_insurance'], true) : 'NOT SET'));
    error_log("  car_finance_noc: " . (isset($_POST['car_finance_noc']) ? print_r($_POST['car_finance_noc'], true) : 'NOT SET'));
    error_log("  car_purchase_invoice: " . (isset($_POST['car_purchase_invoice']) ? print_r($_POST['car_purchase_invoice'], true) : 'NOT SET'));
    error_log("  bifuel_certification: " . (isset($_POST['bifuel_certification']) ? print_r($_POST['bifuel_certification'], true) : 'NOT SET'));
    
    // Validate required fields
    $requiredFields = ['booking_id', 'inspection_delayed'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field missing: $field");
        }
    }
    
    // Handle file uploads - Process ALL files
    $uploadedFiles = [];
    $fileCount = 0;
    
    // Handle all file uploads from $_FILES
    if (!empty($_FILES)) {
        foreach ($_FILES as $fieldName => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                try {
                    $uploadedPath = handleFileUpload($file, UPLOAD_DIR);
                    $uploadedFiles[$fieldName . '_path'] = $uploadedPath;
                    $fileCount++;
                } catch (Exception $e) {
                    error_log("Error uploading $fieldName: " . $e->getMessage());
                    // Continue with other files
                }
            } elseif ($file['error'] === UPLOAD_ERR_NO_FILE) {
                // Check if there's a saved draft file for this field
                $existingFileKey = 'existing_' . $fieldName;
                if (isset($_POST[$existingFileKey]) && !empty($_POST[$existingFileKey])) {
                    $draftPath = $_POST[$existingFileKey];
                    if (file_exists($draftPath)) {
                        // Use draft file directly (already uploaded)
                        $uploadedFiles[$fieldName . '_path'] = $draftPath;
                        $fileCount++;
                    }
                }
            } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                error_log("File upload error for $fieldName: " . $file['error']);
                // Continue with other files instead of throwing exception
            }
        }
    }
    
    // Also check for existing_ fields in POST (from progressive upload)
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'existing_') === 0 && !empty($value)) {
            $fieldName = str_replace('existing_', '', $key);
            $pathKey = $fieldName . '_path';
            
            // Only add if not already added from $_FILES
            if (!isset($uploadedFiles[$pathKey]) && file_exists($value)) {
                $uploadedFiles[$pathKey] = $value;
                $fileCount++;
            }
        }
    }
    
    error_log("Total files processed: $fileCount");
    
    // Prepare data for PDF
    $formData = $_POST;
    
    // Merge uploaded file paths
    $formData = array_merge($formData, $uploadedFiles);
    
    // Log data being sent to PDF
    error_log("Generating PDF with " . count($uploadedFiles) . " images");
    
    // Generate PDF
    require_once 'generate-pdf.php';
    $pdfPath = generatePDF($formData);
    
    if (!$pdfPath || !file_exists($pdfPath)) {
        throw new Exception('Failed to generate PDF');
    }
    
    error_log("PDF generated successfully: $pdfPath");
    
    // Send email with PDF attachment
    require_once 'send-email.php';
    $emailSent = sendEmail($pdfPath, $formData);
    
    if (!$emailSent) {
        // Log email error but don't fail the submission
        logError('Email sending failed, but PDF was generated successfully', ['pdf_path' => $pdfPath]);
        $response['success'] = true;
        $response['message'] = 'Inspection submitted successfully! PDF generated at: ' . $pdfPath . ' (Email delivery failed - please check SMTP settings)';
        $response['pdf_path'] = $pdfPath;
        $response['images_processed'] = $fileCount;
    } else {
        $response['success'] = true;
        $response['message'] = SUCCESS_MESSAGE;
        $response['pdf_path'] = $pdfPath;
        $response['images_processed'] = $fileCount;
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['error_details'] = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
    error_log('Submission error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    logError('Submission error: ' . $e->getMessage(), $_POST);
}

// Clean output and send JSON
ob_end_clean();
echo json_encode($response);
exit;

/**
 * Handle file upload with validation
 */
function handleFileUpload($file, $uploadDir) {
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file extension (more reliable than MIME type)
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        throw new Exception('Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.');
    }
    
    // Validate file size
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('File size exceeds 5MB limit');
    }
    
    // Generate unique filename
    $filename = 'car_photo_' . time() . '_' . uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    return $targetPath;
}
?>
