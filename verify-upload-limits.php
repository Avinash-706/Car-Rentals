<?php
/**
 * Upload Limits Verification Script
 * Verifies that all PHP upload limits are properly configured
 */

require_once 'auto-config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Limits Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status.good {
            background-color: #4CAF50;
            color: white;
        }
        .status.warning {
            background-color: #ff9800;
            color: white;
        }
        .status.bad {
            background-color: #f44336;
            color: white;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .success-box {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
        }
        .error-box {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Upload Limits Verification Report</h1>
        
        <?php
        // Get current PHP settings
        $settings = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_input_vars' => ini_get('max_input_vars'),
        ];
        
        // Recommended values
        $recommended = [
            'upload_max_filesize' => '200M',
            'post_max_size' => '500M',
            'max_file_uploads' => '500',
            'max_execution_time' => '600',
            'max_input_time' => '600',
            'memory_limit' => '2048M',
            'max_input_vars' => '5000',
        ];
        
        // Convert to bytes for comparison
        function convertToBytes($value) {
            $value = trim($value);
            $last = strtolower($value[strlen($value)-1]);
            $value = (int)$value;
            
            switch($last) {
                case 'g': $value *= 1024;
                case 'm': $value *= 1024;
                case 'k': $value *= 1024;
            }
            
            return $value;
        }
        
        // Check status
        function checkStatus($current, $recommended) {
            $currentBytes = is_numeric($current) ? (int)$current : convertToBytes($current);
            $recommendedBytes = is_numeric($recommended) ? (int)$recommended : convertToBytes($recommended);
            
            if ($currentBytes >= $recommendedBytes) {
                return 'good';
            } elseif ($currentBytes >= $recommendedBytes * 0.5) {
                return 'warning';
            } else {
                return 'bad';
            }
        }
        
        $allGood = true;
        foreach ($settings as $key => $value) {
            if (checkStatus($value, $recommended[$key]) !== 'good') {
                $allGood = false;
                break;
            }
        }
        
        if ($allGood) {
            echo '<div class="success-box">';
            echo '<strong>✅ All settings are properly configured!</strong><br>';
            echo 'Your system is ready to handle 500+ image uploads.';
            echo '</div>';
        } else {
            echo '<div class="warning-box">';
            echo '<strong>⚠️ Some settings need attention</strong><br>';
            echo 'Review the table below for details.';
            echo '</div>';
        }
        ?>
        
        <h2>Current PHP Configuration</h2>
        <table>
            <thead>
                <tr>
                    <th>Setting</th>
                    <th>Current Value</th>
                    <th>Recommended</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($settings as $key => $value): 
                    $status = checkStatus($value, $recommended[$key]);
                    $statusText = $status === 'good' ? '✅ Good' : ($status === 'warning' ? '⚠️ Low' : '❌ Too Low');
                ?>
                <tr>
                    <td><strong><?php echo $key; ?></strong></td>
                    <td><?php echo $value; ?></td>
                    <td><?php echo $recommended[$key]; ?></td>
                    <td><span class="status <?php echo $status; ?>"><?php echo $statusText; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>System Information</h2>
        <table>
            <tbody>
                <tr>
                    <td><strong>PHP Version</strong></td>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td><strong>Server Software</strong></td>
                    <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                </tr>
                <tr>
                    <td><strong>PHP SAPI</strong></td>
                    <td><?php echo php_sapi_name(); ?></td>
                </tr>
                <tr>
                    <td><strong>Loaded php.ini</strong></td>
                    <td><?php echo php_ini_loaded_file() ?: 'None'; ?></td>
                </tr>
                <tr>
                    <td><strong>Additional .ini files</strong></td>
                    <td><?php echo php_ini_scanned_files() ?: 'None'; ?></td>
                </tr>
            </tbody>
        </table>
        
        <h2>File Upload Test</h2>
        <div class="info-box">
            <strong>📊 Theoretical Capacity:</strong><br>
            With current settings, you can upload:<br>
            • <strong><?php echo $settings['max_file_uploads']; ?></strong> files per request<br>
            • Each file up to <strong><?php echo $settings['upload_max_filesize']; ?></strong><br>
            • Total POST size up to <strong><?php echo $settings['post_max_size']; ?></strong><br>
            • Processing time up to <strong><?php echo $settings['max_execution_time']; ?></strong> seconds
        </div>
        
        <?php
        // Check if uploads directory is writable
        $uploadDir = 'uploads/drafts/';
        $isWritable = is_writable($uploadDir);
        ?>
        
        <h2>Directory Permissions</h2>
        <table>
            <tbody>
                <tr>
                    <td><strong>Upload Directory</strong></td>
                    <td><?php echo $uploadDir; ?></td>
                    <td>
                        <?php if ($isWritable): ?>
                            <span class="status good">✅ Writable</span>
                        <?php else: ?>
                            <span class="status bad">❌ Not Writable</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h2>Configuration Files</h2>
        <div class="info-box">
            <strong>📝 Configuration is applied through:</strong><br>
            1. <code>php.ini</code> - System-wide PHP configuration<br>
            2. <code>.htaccess</code> - Apache-specific overrides<br>
            3. <code>auto-config.php</code> - Runtime ini_set() calls<br>
            4. Individual PHP files - Script-specific overrides
        </div>
        
        <?php if (!$allGood): ?>
        <h2>Recommendations</h2>
        <div class="warning-box">
            <strong>⚙️ To fix low settings:</strong><br><br>
            
            <strong>1. Update php.ini:</strong><br>
            <code>
            upload_max_filesize = 200M<br>
            post_max_size = 500M<br>
            max_file_uploads = 500<br>
            max_execution_time = 600<br>
            max_input_time = 600<br>
            memory_limit = 2048M<br>
            max_input_vars = 5000
            </code><br><br>
            
            <strong>2. Restart your web server</strong> (Apache/Nginx/PHP-FPM)<br><br>
            
            <strong>3. Verify changes</strong> by refreshing this page
        </div>
        <?php endif; ?>
        
        <div class="info-box" style="margin-top: 30px;">
            <strong>ℹ️ Note:</strong> The system uses progressive upload (one image at a time via AJAX), 
            which bypasses most PHP upload limits. However, having proper limits ensures the draft save 
            and final submission work correctly with all images.
        </div>
    </div>
</body>
</html>
