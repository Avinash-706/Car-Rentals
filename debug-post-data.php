<?php
/**
 * Debug Script to Check POST Data
 * Place this temporarily in submit.php to see what's being received
 */

echo "================================================================================\n";
echo "POST DATA DEBUG\n";
echo "================================================================================\n\n";

echo "RAW POST DATA:\n";
echo "--------------------------------------------------------------------------------\n";
print_r($_POST);
echo "\n\n";

echo "STEP 4 CHECKBOX FIELDS:\n";
echo "--------------------------------------------------------------------------------\n";

$step4Fields = [
    'registration_certificate',
    'car_insurance',
    'car_finance_noc',
    'car_purchase_invoice',
    'bifuel_certification'
];

foreach ($step4Fields as $field) {
    echo "$field:\n";
    if (isset($_POST[$field])) {
        echo "  Type: " . gettype($_POST[$field]) . "\n";
        echo "  Value: " . print_r($_POST[$field], true) . "\n";
        if (is_array($_POST[$field])) {
            echo "  Count: " . count($_POST[$field]) . "\n";
            echo "  Items: " . implode(', ', $_POST[$field]) . "\n";
        }
    } else {
        echo "  NOT SET IN POST\n";
    }
    echo "\n";
}

echo "================================================================================\n";
echo "FORMATTED OUTPUT (using formatArray):\n";
echo "================================================================================\n";

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

foreach ($step4Fields as $field) {
    $value = $_POST[$field] ?? [];
    $formatted = formatArray($value);
    echo "$field: ";
    if ($formatted === '') {
        echo "[EMPTY - would show 'Not Selected']\n";
    } else {
        echo "$formatted\n";
    }
}

echo "\n";
echo "================================================================================\n";
