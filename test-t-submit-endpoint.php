<?php
/**
 * Test if t-submit.php endpoint is working
 */

echo "Testing t-submit.php endpoint...\n\n";

// Simulate POST data
$postData = [
    'test_mode' => 'true',
    'current_step' => '4',
    'total_steps' => '23',
    'booking_id' => 'TEST123',
    'inspection_delayed' => 'No',
    'registration_certificate' => ['Available'],
    'car_insurance' => ['Available', 'Expired'],
];

// Create POST string
$postString = http_build_query($postData);

// Make request
$ch = curl_init('http://localhost:8000/t-submit.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

// Try to decode JSON
$json = json_decode($response, true);

if ($json === null) {
    echo "❌ JSON DECODE FAILED\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "\nRaw response (first 500 chars):\n";
    echo substr($response, 0, 500) . "\n";
} else {
    echo "✅ JSON DECODED SUCCESSFULLY\n";
    print_r($json);
}
