<?php
/**
 * Simulate POST data to test PDF generation
 * This simulates what should be received from the form
 */

// Simulate POST data as it should come from the form
$_POST = [
    'booking_id' => 'TEST123',
    'inspection_delayed' => 'No',
    
    // Step 4 - Car Documents (checkboxes)
    'registration_certificate' => ['Available'],  // Single selection
    'car_insurance' => ['Available', 'Expired'],  // Multiple selections
    'car_finance_noc' => [],  // No selection (empty array)
    'car_purchase_invoice' => ['Not Available'],  // Single selection
    'bifuel_certification' => ['Not Required'],  // Single selection
    
    // Step 5 - Body Frame (checkboxes)
    'radiator_core' => ['OK'],
    'driver_strut' => ['Accidental', 'Rusted'],
    'passenger_strut' => ['OK'],
    'front_bonnet' => [],  // Empty
    'boot_floor' => ['Scratches', 'Repainted'],
];

echo "================================================================================\n";
echo "POST DATA SIMULATION TEST\n";
echo "================================================================================\n\n";

// Include the formatArray function
function formatArray($value) {
    if (is_array($value)) {
        $filtered = array_filter($value, function($v) {
            return $v !== '' && $v !== null && $v !== false;
        });
        
        if (!empty($filtered)) {
            return implode(', ', $filtered);
        }
        return '';
    }
    return (string)$value;
}

// Test each field
$testFields = [
    'registration_certificate' => 'Registration Certificate',
    'car_insurance' => 'Car Insurance',
    'car_finance_noc' => 'Car Finance NOC',
    'car_purchase_invoice' => 'Car Purchase Invoice',
    'bifuel_certification' => 'Bi-Fuel Certification',
    'radiator_core' => 'Radiator Core Support',
    'driver_strut' => 'Driver Side Strut Tower Apron',
    'passenger_strut' => 'Passenger Strut Tower Apron',
    'front_bonnet' => 'Front Bonnet UnderBody',
    'boot_floor' => 'Boot Floor',
];

echo "EXPECTED PDF OUTPUT:\n";
echo "--------------------------------------------------------------------------------\n";

foreach ($testFields as $field => $label) {
    $value = $_POST[$field] ?? [];
    $formatted = formatArray($value);
    
    echo sprintf("%-35s | ", $label);
    
    if ($formatted === '') {
        echo "Not Selected\n";
    } else {
        echo "$formatted\n";
    }
}

echo "\n";
echo "ACTUAL POST DATA:\n";
echo "--------------------------------------------------------------------------------\n";
print_r($_POST);

echo "\n";
echo "================================================================================\n";
echo "CONCLUSION:\n";
echo "================================================================================\n";
echo "If the form is submitting data like this simulation, the PDF should work.\n";
echo "If the PDF still shows 'Not Selected', the problem is in the form submission.\n";
echo "\n";
echo "NEXT STEPS:\n";
echo "1. Add this to submit.php temporarily:\n";
echo "   error_log('POST data: ' . print_r(\$_POST, true));\n";
echo "2. Submit the form\n";
echo "3. Check the error log to see what's actually being received\n";
echo "4. Compare with this simulation\n";
echo "================================================================================\n";
