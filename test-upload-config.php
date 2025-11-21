<?php
/**
 * Quick Upload Configuration Test
 * Run this to verify your upload limits are properly set
 */

require_once 'auto-config.php';

header('Content-Type: application/json');

$config = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit'),
    'max_input_vars' => ini_get('max_input_vars'),
];

$required = [
    'upload_max_filesize' => 200 * 1024 * 1024, // 200M in bytes
    'post_max_size' => 500 * 1024 * 1024, // 500M in bytes
    'max_file_uploads' => 500,
    'max_execution_time' => 600,
    'max_input_time' => 600,
    'memory_limit' => 2048 * 1024 * 1024, // 2048M in bytes
    'max_input_vars' => 5000,
];

function convertToBytes($value) {
    $value = trim($value);
    $last = strtolower($value[strlen($value)-1]);
    $num = (int)$value;
    
    switch($last) {
        case 'g': $num *= 1024;
        case 'm': $num *= 1024;
        case 'k': $num *= 1024;
    }
    
    return $num;
}

$results = [];
$allPassed = true;

foreach ($config as $key => $value) {
    $currentBytes = is_numeric($value) ? (int)$value : convertToBytes($value);
    $requiredBytes = $required[$key];
    $passed = $currentBytes >= $requiredBytes;
    
    if (!$passed) {
        $allPassed = false;
    }
    
    $results[$key] = [
        'current' => $value,
        'current_bytes' => $currentBytes,
        'required_bytes' => $requiredBytes,
        'passed' => $passed,
        'status' => $passed ? 'PASS' : 'FAIL'
    ];
}

$response = [
    'success' => $allPassed,
    'message' => $allPassed ? 'All upload limits are properly configured!' : 'Some upload limits need adjustment',
    'php_version' => phpversion(),
    'sapi' => php_sapi_name(),
    'config_file' => php_ini_loaded_file(),
    'results' => $results,
    'summary' => [
        'total_checks' => count($results),
        'passed' => array_sum(array_column($results, 'passed')),
        'failed' => count($results) - array_sum(array_column($results, 'passed'))
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
