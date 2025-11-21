<?php
// Read the current index.php
$content = file_get_contents('index.php');

// Define the old content to replace
$oldContent = '            <div class="form-step" data-step="9">
                <h2>⊙ Step 9 - Electrical System</h2>
                <div class="form-group">
                    <label>Electrical System Status</label>
                    <textarea name="electrical_status" rows="3"></textarea>
                </div>
            </div>

            <div class="form-step" data-step="10">
                <h2>⊙ Step 10 - Suspension</h2>
                <div class="form-group">
                    <label>Suspension Condition</label>
                    <textarea name="suspension_condition" rows="3"></textarea>
                </div>
            </div>';

// Define the new content
$newContent = file_get_contents('steps_9_10_new.html');

// Replace
$content = str_replace($oldContent, $newContent, $content);

// Write back
file_put_contents('index.php', $content);

echo "Steps 9 and 10 updated successfully!\n";
?>
