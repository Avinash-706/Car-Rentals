<?php
/**
 * PDF Generation Test Cases
 * Run: php test-pdf-generation.php
 */

require_once 'auto-config.php';
define('APP_INIT', true);
require_once 'config.php';
require_once 'generate-pdf.php';

echo "=== PDF Generation Test Suite ===\n\n";

// Test Case 1: All mandatory fields filled
function testCase1() {
    echo "Test 1: All mandatory fields filled, optional empty\n";
    
    $data = [
        'booking_id' => 'TEST001',
        'inspection_delayed' => 'No',
        'car_photo_path' => 'uploads/test_car.jpg',
        'car_company' => 'Toyota',
        'car_registration_number' => 'DL01AB1234',
        'car_registration_year' => '2020',
        'car_variant' => 'Innova Crysta',
        'car_registered_state' => 'Delhi',
        'car_registered_city' => 'New Delhi',
        'fuel_type' => ['Diesel'],
        'engine_capacity' => '2400',
        'transmission' => 'Manual',
        'car_colour' => 'White',
        'car_km_reading' => '50000',
        'car_km_photo_path' => 'uploads/test_km.jpg',
        'car_keys_available' => '2',
        'chassis_number' => 'MA3ERLF3S00123456',
        'chassis_plate_photo_path' => 'uploads/test_chassis.jpg',
        'engine_number' => '1GD123456',
        'registration_certificate' => ['Available'],
        'car_insurance' => ['Available'],
        'car_finance_noc' => ['Not Required'],
        'car_purchase_invoice' => ['Available'],
        'bifuel_certification' => ['Not Required'],
        // ... add all other mandatory fields
        'electrical_status' => 'All electrical components working',
        'suspension_condition' => 'Good condition',
        'transmission_status' => 'Smooth shifting',
        'ac_heating_status' => 'Working properly',
        'safety_features' => 'All airbags present',
        'test_drive_notes' => 'Smooth drive, no issues',
        'overall_assessment' => 'Good condition vehicle',
        'final_remarks' => 'Recommended for purchase',
        'recommended_price' => '1200000',
    ];
    
    $result = generatePDF($data);
    echo $result ? "✓ PASS: PDF generated at $result\n" : "✗ FAIL: PDF generation failed\n";
    echo "\n";
}

// Test Case 2: Some mandatory fields missing
function testCase2() {
    echo "Test 2: Some mandatory fields missing\n";
    
    $data = [
        'booking_id' => 'TEST002',
        'inspection_delayed' => 'Yes',
        // Missing car_photo
        'car_company' => 'Honda',
        // Missing car_registration_number
        'car_registration_year' => '2019',
        // ... some fields missing
    ];
    
    $result = generatePDF($data);
    echo $result ? "✓ PASS: PDF generated with missing fields highlighted\n" : "✗ FAIL\n";
    
    // Check if verification report was created
    $reports = glob('tmp/pdf-verify-*.json');
    if (!empty($reports)) {
        $latestReport = end($reports);
        $reportData = json_decode(file_get_contents($latestReport), true);
        echo "  Missing mandatory fields: " . $reportData['missing_mandatory_count'] . "\n";
    }
    echo "\n";
}

// Test Case 3: Images uploaded for several steps
function testCase3() {
    echo "Test 3: Images uploaded for multiple steps\n";
    
    // Create test images
    createTestImage('uploads/test_car.jpg');
    createTestImage('uploads/test_km.jpg');
    createTestImage('uploads/test_chassis.jpg');
    createTestImage('uploads/test_engine.jpg');
    
    $data = [
        'booking_id' => 'TEST003',
        'inspection_delayed' => 'No',
        'car_photo_path' => 'uploads/test_car.jpg',
        'car_km_photo_path' => 'uploads/test_km.jpg',
        'chassis_plate_photo_path' => 'uploads/test_chassis.jpg',
        'car_start_image_path' => 'uploads/test_engine.jpg',
        // ... other required fields
        'car_company' => 'Maruti',
        'car_registration_number' => 'MH01CD5678',
        'car_registration_year' => '2021',
        'car_variant' => 'Swift VXI',
        'car_registered_state' => 'Maharashtra',
        'car_registered_city' => 'Mumbai',
        'fuel_type' => ['Petrol'],
        'engine_capacity' => '1200',
        'transmission' => 'Manual',
        'car_colour' => 'Red',
        'car_km_reading' => '30000',
        'car_keys_available' => '2',
        'chassis_number' => 'MA3ERLF3S00654321',
        'engine_number' => 'K12M987654',
        'electrical_status' => 'Good',
        'suspension_condition' => 'Good',
        'transmission_status' => 'Good',
        'ac_heating_status' => 'Good',
        'safety_features' => 'Present',
        'test_drive_notes' => 'Good',
        'overall_assessment' => 'Good',
        'final_remarks' => 'Good',
        'recommended_price' => '500000',
    ];
    
    $result = generatePDF($data);
    echo $result ? "✓ PASS: PDF with images generated\n" : "✗ FAIL\n";
    echo "\n";
}

// Test Case 4: Draft-loaded images
function testCase4() {
    echo "Test 4: Draft-loaded images (saved earlier)\n";
    
    $data = [
        'booking_id' => 'TEST004',
        'inspection_delayed' => 'No',
        'car_photo_path' => 'uploads/drafts/1234567890_abc_car.jpg',
        'car_km_photo_path' => 'uploads/drafts/1234567891_def_km.jpg',
        // ... other fields
        'car_company' => 'Hyundai',
        'car_registration_number' => 'KA01EF9012',
        'car_registration_year' => '2022',
        'car_variant' => 'Creta SX',
        'car_registered_state' => 'Karnataka',
        'car_registered_city' => 'Bangalore',
        'fuel_type' => ['Diesel'],
        'engine_capacity' => '1500',
        'transmission' => 'Automatic',
        'car_colour' => 'Blue',
        'car_km_reading' => '15000',
        'car_keys_available' => '2',
        'chassis_number' => 'MA3ERLF3S00111222',
        'engine_number' => 'D4FA333444',
        'electrical_status' => 'Excellent',
        'suspension_condition' => 'Excellent',
        'transmission_status' => 'Excellent',
        'ac_heating_status' => 'Excellent',
        'safety_features' => 'All present',
        'test_drive_notes' => 'Excellent',
        'overall_assessment' => 'Excellent',
        'final_remarks' => 'Highly recommended',
        'recommended_price' => '1500000',
    ];
    
    // Create draft images
    if (!file_exists('uploads/drafts')) {
        mkdir('uploads/drafts', 0755, true);
    }
    createTestImage('uploads/drafts/1234567890_abc_car.jpg');
    createTestImage('uploads/drafts/1234567891_def_km.jpg');
    
    $result = generatePDF($data);
    echo $result ? "✓ PASS: PDF with draft images generated\n" : "✗ FAIL\n";
    echo "\n";
}

// Test Case 5: Large number of files
function testCase5() {
    echo "Test 5: Large number of files (50+ images)\n";
    
    $data = [
        'booking_id' => 'TEST005',
        'inspection_delayed' => 'No',
    ];
    
    // Add all possible image fields
    $imageFields = [
        'car_photo', 'car_km_photo', 'chassis_plate_photo', 'radiator_core_image',
        'driver_strut_image', 'passenger_strut_image', 'front_bonnet_image',
        'boot_floor_image', 'car_start_image', 'wiring_image', 'engine_oil_image',
        'smoke_emission_image', 'obd_scan_photo', 'multi_function_display_image',
        'car_roof_inside_image', 'ac_cool_image', 'ac_hot_image',
        'driver_front_tyre_image', 'driver_back_tyre_image', 'passenger_back_tyre_image',
        'passenger_front_tyre_image', 'stepney_tyre_image', 'oil_leak_image',
    ];
    
    foreach ($imageFields as $field) {
        $filename = "uploads/test_{$field}.jpg";
        createTestImage($filename);
        $data[$field . '_path'] = $filename;
    }
    
    // Add other mandatory fields
    $data['car_company'] = 'Test Company';
    $data['car_registration_number'] = 'TEST1234';
    $data['car_registration_year'] = '2023';
    $data['car_variant'] = 'Test Variant';
    $data['car_registered_state'] = 'Test State';
    $data['car_registered_city'] = 'Test City';
    $data['fuel_type'] = ['Petrol'];
    $data['engine_capacity'] = '1500';
    $data['transmission'] = 'Manual';
    $data['car_colour'] = 'Black';
    $data['car_km_reading'] = '10000';
    $data['car_keys_available'] = '2';
    $data['chassis_number'] = 'TEST123456';
    $data['engine_number'] = 'TEST789';
    $data['electrical_status'] = 'Test';
    $data['suspension_condition'] = 'Test';
    $data['transmission_status'] = 'Test';
    $data['ac_heating_status'] = 'Test';
    $data['safety_features'] = 'Test';
    $data['test_drive_notes'] = 'Test';
    $data['overall_assessment'] = 'Test';
    $data['final_remarks'] = 'Test';
    $data['recommended_price'] = '1000000';
    
    $result = generatePDF($data);
    echo $result ? "✓ PASS: PDF with 50+ images generated\n" : "✗ FAIL\n";
    
    // Check verification report
    $reports = glob('tmp/pdf-verify-*.json');
    if (!empty($reports)) {
        echo "✓ Verification report generated\n";
    }
    echo "\n";
}

// Helper function to create test image
function createTestImage($path) {
    $dir = dirname($path);
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Create a simple test image
    $img = imagecreatetruecolor(800, 600);
    $bgColor = imagecolorallocate($img, 200, 200, 200);
    $textColor = imagecolorallocate($img, 0, 0, 0);
    imagefill($img, 0, 0, $bgColor);
    imagestring($img, 5, 300, 290, 'TEST IMAGE', $textColor);
    imagejpeg($img, $path, 80);
    imagedestroy($img);
}

// Run all tests
echo "Starting tests...\n\n";

testCase1();
testCase2();
testCase3();
testCase4();
testCase5();

echo "=== Test Suite Complete ===\n";
echo "\nCheck the following:\n";
echo "- PDFs in pdfs/ directory\n";
echo "- Verification reports in tmp/ directory\n";
echo "- Logs in logs/pdf_generation.log\n";
