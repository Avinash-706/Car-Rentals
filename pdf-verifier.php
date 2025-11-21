<?php
/**
 * PDF Field Verifier
 * Ensures all fields are properly included in PDF generation
 */

class PDFVerifier {
    
    private $schema;
    private $data;
    private $report = [];
    private $missingMandatory = [];
    private $logFile = 'logs/pdf_generation.log';
    
    public function __construct($data) {
        $this->schema = require __DIR__ . '/form-schema.php';
        $this->data = $data;
        
        // Ensure log directory exists
        if (!file_exists('logs')) {
            mkdir('logs', 0755, true);
        }
    }
    
    /**
     * Verify all fields against schema
     */
    public function verify() {
        $this->log("=== PDF Verification Started ===");
        
        foreach ($this->schema as $stepNumber => $step) {
            $stepReport = [
                'step' => $stepNumber,
                'title' => $step['title'],
                'fields' => []
            ];
            
            foreach ($step['fields'] as $fieldKey => $fieldConfig) {
                $fieldStatus = $this->verifyField($stepNumber, $fieldKey, $fieldConfig);
                $stepReport['fields'][] = $fieldStatus;
                
                // Track missing mandatory fields
                if ($fieldConfig['required'] && $fieldStatus['status'] === 'missing') {
                    $this->missingMandatory[] = [
                        'step' => $stepNumber,
                        'field' => $fieldKey,
                        'label' => $fieldConfig['label']
                    ];
                }
            }
            
            $this->report[] = $stepReport;
        }
        
        $this->log("=== PDF Verification Completed ===");
        $this->log("Missing Mandatory Fields: " . count($this->missingMandatory));
        
        return $this->report;
    }
    
    /**
     * Verify individual field
     */
    private function verifyField($step, $fieldKey, $config) {
        $status = [
            'step' => $step,
            'field_key' => $fieldKey,
            'label' => $config['label'],
            'type' => $config['type'],
            'required' => $config['required'],
            'status' => 'unknown',
            'value' => null,
            'filepath' => null
        ];
        
        // Check for file fields
        if ($config['type'] === 'file') {
            $pathKey = $fieldKey . '_path';
            
            if (isset($this->data[$pathKey]) && !empty($this->data[$pathKey])) {
                $filepath = $this->data[$pathKey];
                
                // Verify file exists
                if (file_exists($filepath)) {
                    $status['status'] = 'included';
                    $status['filepath'] = $filepath;
                    $status['filesize'] = filesize($filepath);
                } else {
                    $status['status'] = 'file_missing';
                    $status['filepath'] = $filepath;
                    $this->log("WARNING: File not found - Step $step, Field: {$config['label']}, Path: $filepath");
                }
            } else {
                $status['status'] = $config['required'] ? 'missing' : 'omitted_optional';
                
                if ($config['required']) {
                    $this->log("ERROR: Required file missing - Step $step, Field: {$config['label']}");
                }
            }
        } else {
            // Check for regular fields
            if (isset($this->data[$fieldKey]) && $this->data[$fieldKey] !== '' && $this->data[$fieldKey] !== null) {
                $status['status'] = 'included';
                $status['value'] = $this->data[$fieldKey];
            } else {
                $status['status'] = $config['required'] ? 'missing' : 'omitted_optional';
                
                if ($config['required']) {
                    $this->log("ERROR: Required field missing - Step $step, Field: {$config['label']}");
                }
            }
        }
        
        return $status;
    }
    
    /**
     * Get missing mandatory fields
     */
    public function getMissingMandatory() {
        return $this->missingMandatory;
    }
    
    /**
     * Get full report
     */
    public function getReport() {
        return $this->report;
    }
    
    /**
     * Save report to JSON
     */
    public function saveReport() {
        $timestamp = date('YmdHis');
        $reportFile = "tmp/pdf-verify-{$timestamp}.json";
        
        $reportData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'total_steps' => count($this->schema),
            'missing_mandatory_count' => count($this->missingMandatory),
            'missing_mandatory' => $this->missingMandatory,
            'full_report' => $this->report
        ];
        
        file_put_contents($reportFile, json_encode($reportData, JSON_PRETTY_PRINT));
        $this->log("Report saved to: $reportFile");
        
        return $reportFile;
    }
    
    /**
     * Generate missing fields summary HTML
     */
    public function generateMissingSummaryHTML() {
        if (empty($this->missingMandatory)) {
            return '';
        }
        
        $html = '
        <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin: 20px 0; page-break-after: always;">
            <h2 style="color: #856404; margin-top: 0;">⚠️ MISSING MANDATORY FIELDS</h2>
            <p style="color: #856404;">The following required fields were not provided:</p>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #ffc107;">
                        <th style="padding: 8px; text-align: left; border: 1px solid #856404;">Step</th>
                        <th style="padding: 8px; text-align: left; border: 1px solid #856404;">Field</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($this->missingMandatory as $missing) {
            $html .= '
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Step ' . $missing['step'] . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($missing['label']) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
        </div>';
        
        return $html;
    }
    
    /**
     * Log message
     */
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}
