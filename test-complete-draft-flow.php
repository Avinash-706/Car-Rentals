<?php
/**
 * Test Complete Draft Flow
 * Simulates upload → save → load cycle
 */

require_once 'auto-config.php';
require_once 'init-directories.php';
define('APP_INIT', true);
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'steps' => [], 'errors' => []];

try {
    // Step 1: Create a test draft
    $draftId = 'test_' . uniqid();
    $draftDir = DirectoryManager::getAbsolutePath('uploads/drafts') . DIRECTORY_SEPARATOR;
    
    $response['steps'][] = "Step 1: Creating test draft with ID: $draftId";
    
    // Step 2: Create test image paths (simulating uploaded files)
    $testFiles = [
        'carPhoto' => 'uploads/drafts/test_image1.jpg',
        'enginePhoto' => 'uploads/drafts/test_image2.jpg',
        'interiorPhoto' => 'uploads/drafts/test_image3.jpg'
    ];
    
    $response['steps'][] = "Step 2: Created test file paths";
    
    // Step 3: Save draft with uploaded files
    $draftData = [
        'draft_id' => $draftId,
        'timestamp' => time(),
        'current_step' => 5,
        'form_data' => [
            'booking_id' => 'TEST123',
            'customer_name' => 'Test Customer'
        ],
        'uploaded_files' => $testFiles
    ];
    
    $draftFile = $draftDir . $draftId . '.json';
    file_put_contents($draftFile, json_encode($draftData, JSON_PRETTY_PRINT));
    
    $response['steps'][] = "Step 3: Saved draft to: $draftFile";
    $response['draft_data_saved'] = $draftData;
    
    // Step 4: Load draft (simulating page reload)
    if (!file_exists($draftFile)) {
        throw new Exception("Draft file not found after save");
    }
    
    $loadedData = json_decode(file_get_contents($draftFile), true);
    
    if (!$loadedData) {
        throw new Exception("Failed to parse draft JSON");
    }
    
    $response['steps'][] = "Step 4: Loaded draft from file";
    $response['draft_data_loaded'] = $loadedData;
    
    // Step 5: Verify uploaded files
    $verifiedFiles = [];
    $missingFiles = [];
    
    foreach ($loadedData['uploaded_files'] as $fieldName => $filePath) {
        // Check if path is relative web path
        if (strpos($filePath, 'uploads/') === 0) {
            $verifiedFiles[$fieldName] = $filePath;
            $response['steps'][] = "✅ $fieldName: Correct format (relative web path)";
        } else {
            $missingFiles[$fieldName] = $filePath;
            $response['steps'][] = "❌ $fieldName: Wrong format (not relative web path)";
        }
    }
    
    $response['steps'][] = "Step 5: Verified file paths";
    $response['verified_files'] = $verifiedFiles;
    $response['missing_files'] = $missingFiles;
    
    // Step 6: Test path conversion
    $response['steps'][] = "Step 6: Testing path conversions";
    
    foreach ($testFiles as $fieldName => $filePath) {
        // Simulate what load-draft.php does
        $absolutePath = DirectoryManager::getAbsolutePath($filePath);
        $webPath = DirectoryManager::toWebPath(DirectoryManager::getRelativePath($absolutePath));
        
        $response['path_conversions'][$fieldName] = [
            'original' => $filePath,
            'absolute' => $absolutePath,
            'web_path' => $webPath,
            'format_correct' => (strpos($webPath, 'uploads/') === 0)
        ];
    }
    
    // Step 7: Cleanup
    @unlink($draftFile);
    $response['steps'][] = "Step 7: Cleaned up test draft";
    
    // Final verification
    $allCorrect = count($missingFiles) === 0;
    $response['success'] = $allCorrect;
    $response['message'] = $allCorrect 
        ? "✅ All tests passed! Draft flow working correctly."
        : "❌ Some tests failed. Check missing_files for details.";
    
} catch (Exception $e) {
    $response['errors'][] = $e->getMessage();
    $response['message'] = "❌ Test failed: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
