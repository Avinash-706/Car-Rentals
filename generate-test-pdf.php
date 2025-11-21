<?php
/**
 * Test PDF Generation - Only includes filled steps
 * Standalone version without dependencies
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/image-optimizer.php';

function generateTestPDF($data, $maxStep = 23) {
    try {
        // Create mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'tempDir' => __DIR__ . '/tmp',
        ]);
        
        $mpdf->SetTitle('Test Report - Steps 1-' . $maxStep);
        
        // Generate HTML
        $html = testGenerateHTML($data, $maxStep);
        $mpdf->WriteHTML($html);
        
        // Save PDF
        $pdfFilename = 'TEST_step' . $maxStep . '_' . time() . '.pdf';
        $pdfPath = __DIR__ . '/pdfs/' . $pdfFilename;
        $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);
        
        return $pdfPath;
        
    } catch (Exception $e) {
        error_log('Test PDF Error: ' . $e->getMessage());
        throw $e;
    }
}

function testGenerateHTML($data, $maxStep) {
    $html = '<style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; background: #ff9800; color: white; padding: 15px; }
        .step-header { background: #f0f0f0; padding: 10px; font-weight: bold; margin: 15px 0 10px 0; }
        .field-row { margin: 5px 0; padding: 5px 0; border-bottom: 1px solid #eee; }
        .field-label { font-weight: bold; color: #333; width: 40%; display: inline-block; }
        .field-value { color: #000; width: 58%; display: inline-block; }
        .field-value.missing { color: #ff9800; font-weight: bold; }
    </style>';
    
    $html .= '<div class="header">
        <h1>🔍 TEST REPORT</h1>
        <p>Steps 1-' . $maxStep . ' Only</p>
        <p>Booking: ' . htmlspecialchars($data['booking_id'] ?? 'N/A') . '</p>
    </div>';
    
    // Step 1
    if ($maxStep >= 1) {
        $html .= '<div class="step-header">STEP 1 — BOOKING DETAILS</div>';
        $html .= testField('Booking ID', $data['booking_id'] ?? '');
    }
    
    // Step 2
    if ($maxStep >= 2) {
        $html .= '<div class="step-header">STEP 2 — EXPERT DETAILS</div>';
        $html .= testField('Inspection Delayed', $data['inspection_delayed'] ?? '');
    }
    
    // Step 3
    if ($maxStep >= 3) {
        $html .= '<div class="step-header">STEP 3 — CAR DETAILS</div>';
        $html .= testField('Car Company', $data['car_company'] ?? '');
        $html .= testField('Registration Number', $data['car_registration_number'] ?? '');
        $html .= testField('Fuel Type', testFormatArray($data['fuel_type'] ?? []));
    }
    
    // Step 4
    if ($maxStep >= 4) {
        $html .= '<div class="step-header">STEP 4 — CAR DOCUMENTS</div>';
        $html .= testField('Registration Certificate', testFormatArray($data['registration_certificate'] ?? []));
        $html .= testField('Car Insurance', testFormatArray($data['car_insurance'] ?? []));
        $html .= testField('Car Finance NOC', testFormatArray($data['car_finance_noc'] ?? []));
        $html .= testField('Car Purchase Invoice', testFormatArray($data['car_purchase_invoice'] ?? []));
        $html .= testField('Bi-Fuel Certification', testFormatArray($data['bifuel_certification'] ?? []));
    }
    
    // Step 5
    if ($maxStep >= 5) {
        $html .= '<div class="step-header">STEP 5 — BODY FRAME CHECKLIST</div>';
        $html .= testField('Radiator Core Support', testFormatArray($data['radiator_core'] ?? []));
        $html .= testField('Driver Strut', testFormatArray($data['driver_strut'] ?? []));
        $html .= testField('Passenger Strut', testFormatArray($data['passenger_strut'] ?? []));
        $html .= testField('Front Bonnet', testFormatArray($data['front_bonnet'] ?? []));
        $html .= testField('Boot Floor', testFormatArray($data['boot_floor'] ?? []));
    }
    
    // Steps 6-23 (simplified)
    for ($i = 6; $i <= $maxStep; $i++) {
        $html .= '<div class="step-header">STEP ' . $i . '</div>';
        $html .= '<p style="padding: 10px;">Step ' . $i . ' data included...</p>';
    }
    
    $html .= '<div style="text-align: center; margin-top: 20px; padding: 15px; border-top: 2px solid #ff9800;">
        <p><strong>TEST MODE - Steps 1-' . $maxStep . '</strong></p>
        <p>Generated: ' . date('Y-m-d H:i:s') . '</p>
    </div>';
    
    return $html;
}

function testField($label, $value) {
    $value = (string)$value;
    
    if ($value === '') {
        $value = '<span class="missing">Not Selected</span>';
    } else {
        $value = htmlspecialchars($value);
    }
    
    return '<div class="field-row">
        <span class="field-label">' . htmlspecialchars($label) . ':</span>
        <span class="field-value">' . $value . '</span>
    </div>';
}

function testFormatArray($value) {
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
