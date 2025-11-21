<?php
/**
 * Test Script for Image Upload Fix
 * Verifies all fixes are working correctly
 */

require_once __DIR__ . '/image-optimizer.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Upload Fix Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #2196F3; }
        .test { padding: 15px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #ccc; }
        .pass { background: #d4edda; border-left-color: #28a745; }
        .fail { background: #f8d7da; border-left-color: #dc3545; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .upload-form { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 4px; }
        button { background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1976d2; }
        #result { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧪 Image Upload Fix Test</h1>
    
    <h2>1. GD Extension Check</h2>
    <?php
    $gdLoaded = extension_loaded('gd');
    if ($gdLoaded) {
        echo "<div class='test pass'>✅ GD Extension is loaded</div>";
    } else {
        echo "<div class='test fail'>❌ GD Extension is NOT loaded</div>";
        echo "<div class='test info'>Run <code>check-gd-extension.php</code> for detailed diagnostics</div>";
    }
    ?>
    
    <h2>2. Required Functions Check</h2>
    <?php
    $functions = [
        'imagecreatefromjpeg',
        'imagecreatefrompng',
        'imagecreatefromgif',
        'imagecreatetruecolor',
        'imagecopyresampled',
        'imagejpeg'
    ];
    
    $allFunctionsExist = true;
    foreach ($functions as $func) {
        $exists = function_exists($func);
        if ($exists) {
            echo "<div class='test pass'>✅ <code>$func()</code> is available</div>";
        } else {
            echo "<div class='test fail'>❌ <code>$func()</code> is NOT available</div>";
            $allFunctionsExist = false;
        }
    }
    ?>
    
    <h2>3. Path Handling Test</h2>
    <?php
    $testPaths = [
        'uploads/drafts/test.jpg',
        __DIR__ . '/uploads/test.jpg',
        'C:\\xampp\\htdocs\\test.jpg',
        '/var/www/html/test.jpg'
    ];
    
    echo "<div class='test info'>";
    echo "<strong>Testing path normalization:</strong><br><br>";
    
    // Use reflection to access private method
    $reflection = new ReflectionClass('ImageOptimizer');
    $method = $reflection->getMethod('normalizePath');
    $method->setAccessible(true);
    
    foreach ($testPaths as $path) {
        try {
            $normalized = $method->invoke(null, $path);
            echo "Input: <code>$path</code><br>";
            echo "Output: <code>$normalized</code><br><br>";
        } catch (Exception $e) {
            echo "Error normalizing <code>$path</code>: " . $e->getMessage() . "<br><br>";
        }
    }
    echo "</div>";
    ?>
    
    <h2>4. Directory Check</h2>
    <?php
    $directories = [
        'uploads',
        'uploads/drafts',
        'uploads/compressed',
        'uploads/uniform',
        'tmp'
    ];
    
    foreach ($directories as $dir) {
        $fullPath = __DIR__ . '/' . $dir;
        $exists = file_exists($fullPath);
        $writable = $exists && is_writable($fullPath);
        
        if ($exists && $writable) {
            echo "<div class='test pass'>✅ <code>$dir/</code> exists and is writable</div>";
        } elseif ($exists) {
            echo "<div class='test fail'>❌ <code>$dir/</code> exists but is NOT writable</div>";
        } else {
            echo "<div class='test fail'>❌ <code>$dir/</code> does NOT exist</div>";
        }
    }
    ?>
    
    <h2>5. Live Upload Test</h2>
    <div class="upload-form">
        <p><strong>Test image upload with the fixed code:</strong></p>
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="file" name="image" id="imageInput" accept="image/*" required>
            <input type="hidden" name="field_name" value="test_image">
            <input type="hidden" name="draft_id" value="test_<?php echo time(); ?>">
            <input type="hidden" name="step" value="test">
            <br><br>
            <button type="submit">Upload Test Image</button>
        </form>
        
        <div id="result"></div>
    </div>
    
    <h2>6. JSON Response Test</h2>
    <div class="test info">
        <strong>Testing guaranteed JSON response:</strong><br><br>
        <button onclick="testJSONResponse()">Test JSON Response</button>
        <div id="jsonResult" style="margin-top: 10px;"></div>
    </div>
    
    <script>
    // Upload test
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById('result');
        
        resultDiv.innerHTML = '<p>⏳ Uploading...</p>';
        
        fetch('upload-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                resultDiv.innerHTML = `
                    <div class="test pass">
                        <strong>✅ Upload Successful!</strong><br><br>
                        <strong>Path:</strong> ${data.path}<br>
                        <strong>Size:</strong> ${(data.size / 1024).toFixed(2)} KB<br>
                        <strong>Dimensions:</strong> ${data.width} × ${data.height}<br>
                        <strong>Draft ID:</strong> ${data.draft_id}<br>
                        <strong>Message:</strong> ${data.message}
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="test fail">
                        <strong>❌ Upload Failed</strong><br><br>
                        <strong>Error:</strong> ${data.message}<br>
                        ${data.error_type ? '<strong>Type:</strong> ' + data.error_type + '<br>' : ''}
                        ${data.file ? '<strong>File:</strong> ' + data.file + '<br>' : ''}
                        ${data.line ? '<strong>Line:</strong> ' + data.line : ''}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            resultDiv.innerHTML = `
                <div class="test fail">
                    <strong>❌ Network Error</strong><br><br>
                    ${error.message}
                </div>
            `;
        });
    });
    
    // JSON response test
    function testJSONResponse() {
        const resultDiv = document.getElementById('jsonResult');
        resultDiv.innerHTML = '<p>⏳ Testing...</p>';
        
        // Test with invalid request (should still return valid JSON)
        fetch('upload-image.php', {
            method: 'POST',
            body: new FormData() // Empty form data
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = `
                <div class="test pass">
                    <strong>✅ JSON Response Test Passed!</strong><br><br>
                    Server returned valid JSON even with invalid request:<br>
                    <code>${JSON.stringify(data, null, 2)}</code>
                </div>
            `;
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="test fail">
                    <strong>❌ JSON Response Test Failed</strong><br><br>
                    ${error.message}
                </div>
            `;
        });
    }
    </script>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <strong>Summary:</strong>
        <ul>
            <li>If all tests pass ✅, your system is ready!</li>
            <li>If GD tests fail ❌, run <code>check-gd-extension.php</code> for installation instructions</li>
            <li>If directory tests fail ❌, check folder permissions</li>
            <li>Test the live upload to verify end-to-end functionality</li>
        </ul>
    </div>
</div>
</body>
</html>
