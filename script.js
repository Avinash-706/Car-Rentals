// Multi-step form navigation
let currentStep = 1;
const totalSteps = 23;

// Progressive upload tracking
let uploadedFiles = {};
let draftId = localStorage.getItem('draftId') || null;

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Setup progressive upload
    setupProgressiveUpload();
    // Set current date and time for Step 2
    updateDateTime();
    
    // Load draft if exists
    loadDraft();
    
    // Event listeners
    document.getElementById('nextBtn').addEventListener('click', nextStep);
    document.getElementById('prevBtn').addEventListener('click', prevStep);
    document.getElementById('saveDraftBtn').addEventListener('click', saveDraft);
    document.getElementById('discardDraftBtn').addEventListener('click', discardDraft);
    document.getElementById('tSubmitBtn').addEventListener('click', testSubmit);
    document.getElementById('fetchLocation').addEventListener('click', fetchLocation);
    
    // Submit button - both form submit and direct click
    document.getElementById('inspectionForm').addEventListener('submit', submitForm);
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        e.preventDefault();
        submitForm(e);
    });
    
    // File upload preview
    document.getElementById('carPhoto').addEventListener('change', previewImage);
    
    // Setup all image previews
    setupImagePreviews();
    
    // Setup OK checkbox logic
    setupOkCheckboxLogic();
    
    // Auto-save on input change
    const formInputs = document.querySelectorAll('input, textarea, select');
    formInputs.forEach(input => {
        input.addEventListener('change', autoSave);
    });
    
    updateProgress();
});

function nextStep() {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

function showStep(step) {
    // Hide all steps
    const steps = document.querySelectorAll('.form-step');
    steps.forEach(s => s.classList.remove('active'));
    
    // Show current step
    const currentStepElement = document.querySelector(`[data-step="${step}"]`);
    if (currentStepElement) {
        currentStepElement.classList.add('active');
    }
    
    // Update buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    prevBtn.style.display = step === 1 ? 'none' : 'block';
    nextBtn.style.display = step === totalSteps ? 'none' : 'block';
    submitBtn.style.display = step === totalSteps ? 'block' : 'none';
    
    // Ensure submit button is enabled and clickable on final step
    if (step === totalSteps) {
        submitBtn.disabled = false;
        submitBtn.style.pointerEvents = 'auto';
        submitBtn.style.opacity = '1';
        console.log('Submit button should now be visible and clickable');
    }
    
    updateProgress();
}

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('progressText').textContent = `Step ${currentStep} of ${totalSteps}`;
}

function validateStep(step) {
    const currentStepElement = document.querySelector(`[data-step="${step}"]`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        // Skip file inputs that are not visible or already have files
        if (field.type === 'file') {
            // Check if there's a saved file OR a newly uploaded file
            const hasSavedFile = field.dataset.savedFile;
            const hasNewFile = field.files && field.files.length > 0;
            
            if (!hasSavedFile && !hasNewFile) {
                field.focus();
                alert('Please upload all required images');
                return false;
            }
            
            // Only validate new uploads
            if (hasNewFile) {
                // Validate file size (5MB max)
                if (field.files[0].size > 5242880) {
                    alert('File size must be less than 5MB');
                    return false;
                }
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(field.files[0].type)) {
                    alert('Only JPG and PNG images are allowed');
                    return false;
                }
            }
        } else if (field.type === 'checkbox' || field.type === 'radio') {
            // Check if at least one checkbox/radio in the group is selected
            const groupName = field.name;
            const group = currentStepElement.querySelectorAll(`[name="${groupName}"]`);
            const isChecked = Array.from(group).some(input => input.checked);
            if (!isChecked) {
                field.focus();
                alert('Please select at least one option for: ' + field.closest('.form-group').querySelector('label').textContent);
                return false;
            }
        } else if (!field.value) {
            field.focus();
            alert('Please fill in all required fields');
            return false;
        }
    }
    
    // Special validation for Step 2
    if (step === 2) {
        const lat = document.getElementById('latitude').value;
        const long = document.getElementById('longitude').value;
        
        if (!lat || !long) {
            alert('Please fetch your current location');
            return false;
        }
    }
    
    // Validate Car Registration Year (Step 3)
    if (step === 3) {
        const year = document.querySelector('[name="car_registration_year"]').value;
        if (year && !/^\d{4}$/.test(year)) {
            alert('Car Registration Year must be exactly 4 digits');
            return false;
        }
    }
    
    return true;
}

function updateDateTime() {
    const now = new Date();
    
    // Format date as DD-MM-YYYY
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const dateStr = `${day}-${month}-${year}`;
    
    // Format time as HH:MM AM/PM
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    const timeStr = `${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
    
    document.getElementById('expertDate').value = dateStr;
    document.getElementById('expertTime').value = timeStr;
}

function fetchLocation() {
    const errorDiv = document.getElementById('locationError');
    errorDiv.textContent = '';
    
    if (!navigator.geolocation) {
        errorDiv.textContent = 'Geolocation is not supported by your browser';
        return;
    }
    
    document.getElementById('fetchLocation').textContent = '⏳';
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const long = position.coords.longitude;
            
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = long.toFixed(6);
            
            // Reverse geocoding using OpenStreetMap Nominatim
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${long}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('locationAddress').value = data.display_name || 'Address not found';
                    document.getElementById('fetchLocation').textContent = '📍';
                })
                .catch(error => {
                    document.getElementById('locationAddress').value = `Lat: ${lat}, Long: ${long}`;
                    document.getElementById('fetchLocation').textContent = '📍';
                });
        },
        function(error) {
            errorDiv.textContent = 'Failed to fetch location. Ensure location is enabled in your phone.';
            document.getElementById('fetchLocation').textContent = '📍';
        }
    );
}

function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('carPhotoPreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    }
}

function setupImagePreviews() {
    // Get all file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewId = input.id + 'Preview';
            const preview = document.getElementById(previewId);
            
            if (file && preview) {
                // Check file size
                if (file.size > 5242880) {
                    alert('File size must be less than 5MB');
                    input.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only JPG and PNG images are allowed');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="replace-image-btn" onclick="replaceImage('${input.name}')">Replace Image</button>
                    `;
                };
                reader.readAsDataURL(file);
                
                // Remove saved file marker since user is uploading new file
                delete input.dataset.savedFile;
                
                // Make field required again if it was marked as having saved file
                if (input.hasAttribute('data-was-required')) {
                    input.setAttribute('required', 'required');
                }
            }
        });
    });
}

function setupOkCheckboxLogic() {
    // Find all checkbox groups with OK logic
    const checkboxGroups = document.querySelectorAll('[data-ok-group]');
    
    checkboxGroups.forEach(group => {
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const okCheckbox = group.querySelector('[data-ok-checkbox]');
        
        if (!okCheckbox) return;
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this === okCheckbox) {
                    // If OK is checked, uncheck all others
                    if (this.checked) {
                        checkboxes.forEach(cb => {
                            if (cb !== okCheckbox) {
                                cb.checked = false;
                            }
                        });
                    }
                } else {
                    // If any other checkbox is checked, uncheck OK
                    if (this.checked) {
                        okCheckbox.checked = false;
                    }
                }
            });
        });
    });
}

function saveDraft() {
    console.log('Saving draft...');
    const form = document.getElementById('inspectionForm');
    const formData = new FormData();
    
    // Add current step
    formData.append('current_step', currentStep);
    
    // Add draft ID if exists
    const existingDraftId = localStorage.getItem('draftId') || draftId;
    if (existingDraftId) {
        formData.append('draft_id', existingDraftId);
    }
    
    // Add all form fields (but NOT file inputs)
    const inputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
    inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            if (input.checked) {
                formData.append(input.name, input.value);
            }
        } else if (input.name) {
            formData.append(input.name, input.value);
        }
    });
    
    // Send uploadedFiles as JSON (images already uploaded via upload-image.php)
    if (typeof uploadedFiles !== 'undefined' && Object.keys(uploadedFiles).length > 0) {
        formData.append('uploaded_files_json', JSON.stringify(uploadedFiles));
        console.log('Sending', Object.keys(uploadedFiles).length, 'uploaded file paths');
    }
    
    // Also send savedFiles for backward compatibility
    const savedFiles = JSON.parse(localStorage.getItem('savedFiles') || '{}');
    for (const fieldName in savedFiles) {
        formData.append('existing_' + fieldName, savedFiles[fieldName]);
    }
    
    // Show loading indicator
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const originalText = saveDraftBtn.textContent;
    saveDraftBtn.textContent = 'Saving...';
    saveDraftBtn.disabled = true;
    
    fetch('save-draft.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Check if response is OK
        if (!response.ok) {
            throw new Error('Server returned ' + response.status + ': ' + response.statusText);
        }
        
        // Get the response text first to see what we're getting
        return response.text().then(text => {
            console.log('Raw response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON. Response was:', text);
                throw new Error('Invalid JSON response from server. Check console for details.');
            }
        });
    })
    .then(data => {
        saveDraftBtn.textContent = originalText;
        saveDraftBtn.disabled = false;
        
        if (data.success) {
            // Save draft ID
            localStorage.setItem('draftId', data.draft_id);
            
            // Save file paths
            if (data.draft_data && data.draft_data.uploaded_files) {
                localStorage.setItem('savedFiles', JSON.stringify(data.draft_data.uploaded_files));
            }
            
            alert('Draft saved successfully! ' + (data.files_saved || 0) + ' images saved.');
            console.log('Draft saved:', data);
        } else {
            alert('Error saving draft: ' + data.message);
            console.error('Draft save error:', data);
        }
    })
    .catch(error => {
        saveDraftBtn.textContent = originalText;
        saveDraftBtn.disabled = false;
        alert('Error saving draft: ' + error.message);
        console.error('Draft save error:', error);
    });
}

function loadDraft() {
    console.log('Loading draft...');
    const draftId = localStorage.getItem('draftId');
    
    if (!draftId) {
        console.log('No draft ID found');
        return;
    }
    
    // Fetch draft from server
    fetch('load-draft.php?draft_id=' + draftId)
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            // Draft not found on server, clear local storage
            console.log('Draft not found on server, clearing local storage');
            localStorage.removeItem('draftId');
            localStorage.removeItem('savedFiles');
            localStorage.removeItem('uploadedFiles');
            return;
        }
        
        if (data.success && data.draft_data) {
            console.log('Draft loaded:', data);
            const draftData = data.draft_data;
            
            // Restore current step
            if (draftData.current_step) {
                currentStep = parseInt(draftData.current_step);
            }
            
            // Restore form fields
            if (draftData.form_data) {
                for (let key in draftData.form_data) {
                    const fields = document.querySelectorAll(`[name="${key}"]`);
                    
                    if (fields.length > 0) {
                        const firstField = fields[0];
                        
                        if (firstField.type === 'checkbox') {
                            const values = Array.isArray(draftData.form_data[key]) ? draftData.form_data[key] : [draftData.form_data[key]];
                            fields.forEach(field => {
                                field.checked = values.includes(field.value);
                            });
                        } else if (firstField.type === 'radio') {
                            fields.forEach(field => {
                                field.checked = field.value === draftData.form_data[key];
                            });
                        } else {
                            firstField.value = draftData.form_data[key];
                        }
                    }
                }
            }
            
            // Restore uploaded images
            if (draftData.uploaded_files) {
                localStorage.setItem('savedFiles', JSON.stringify(draftData.uploaded_files));
                
                for (let fieldName in draftData.uploaded_files) {
                    const filePath = draftData.uploaded_files[fieldName];
                    const fileInput = document.querySelector(`[name="${fieldName}"]`);
                    
                    if (fileInput && fileInput.type === 'file') {
                        const previewId = fileInput.id + 'Preview';
                        const preview = document.getElementById(previewId);
                        
                        if (preview) {
                            // Show image preview
                            preview.innerHTML = `
                                <img src="${filePath}" alt="Saved image">
                                <button type="button" class="replace-image-btn" onclick="replaceImage('${fieldName}')">Replace Image</button>
                            `;
                            
                            // Mark field as having saved file
                            fileInput.dataset.savedFile = filePath;
                            fileInput.removeAttribute('required');
                        }
                    }
                }
            }
            
            showStep(currentStep);
            alert('Draft loaded successfully!');
        } else {
            console.log('No draft data or load failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading draft:', error);
    });
}

function replaceImage(fieldName) {
    const fileInput = document.querySelector(`[name="${fieldName}"]`);
    if (fileInput) {
        fileInput.click();
    }
}

function discardDraft() {
    if (confirm('Are you sure you want to discard the draft? All saved data and images will be deleted.')) {
        const draftId = localStorage.getItem('draftId');
        
        // Show loading
        const discardBtn = document.getElementById('discardDraftBtn');
        if (discardBtn) {
            discardBtn.textContent = 'Discarding...';
            discardBtn.disabled = true;
        }
        
        // Delete draft from server (use new endpoint)
        if (draftId) {
            fetch('drafts/discard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'draft_id=' + encodeURIComponent(draftId)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Draft discarded:', data);
                
                // Complete cleanup
                completeDiscardCleanup();
            })
            .catch(error => {
                console.error('Error discarding draft:', error);
                
                // Still do local cleanup even if server fails
                completeDiscardCleanup();
            });
        } else {
            // No draft ID, just clean up locally
            completeDiscardCleanup();
        }
    }
}

function completeDiscardCleanup() {
    // Clear ALL localStorage items
    localStorage.removeItem('draftId');
    localStorage.removeItem('savedFiles');
    localStorage.removeItem('inspectionDraft');
    localStorage.removeItem('uploadedFiles');
    localStorage.clear(); // Nuclear option - clear everything
    
    // Clear sessionStorage too
    sessionStorage.clear();
    
    // Clear global variables
    if (typeof uploadedFiles !== 'undefined') {
        uploadedFiles = {};
    }
    if (typeof draftId !== 'undefined') {
        draftId = null;
    }
    
    // Clear all form inputs
    const form = document.getElementById('inspectionForm');
    if (form) {
        form.reset();
    }
    
    // Clear all file input previews
    const allPreviews = document.querySelectorAll('[id$="Preview"]');
    allPreviews.forEach(preview => {
        preview.innerHTML = '';
    });
    
    // Clear all image previews
    const allImages = document.querySelectorAll('.image-container img');
    allImages.forEach(img => {
        img.remove();
    });
    
    // Remove all "Replace Image" buttons
    const replaceButtons = document.querySelectorAll('.replace-image-btn');
    replaceButtons.forEach(btn => {
        btn.remove();
    });
    
    // Remove all upload success indicators
    const successIndicators = document.querySelectorAll('.upload-success');
    successIndicators.forEach(indicator => {
        indicator.remove();
    });
    
    // Reset all file inputs to required if they were required
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        // Remove saved file marker
        delete input.dataset.savedFile;
        
        // Restore required attribute if it was originally required
        if (input.hasAttribute('data-originally-required')) {
            input.setAttribute('required', 'required');
        }
        
        // Clear the file input value (this is tricky, need to replace the element)
        const newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);
    });
    
    // Reset to step 1
    currentStep = 1;
    showStep(1);
    updateProgress();
    
    // Show success message
    alert('Draft discarded successfully. All data and images have been deleted.');
    
    // Reload page to ensure clean state
    setTimeout(() => {
        location.reload();
    }, 500);
}

function autoSave() {
    // Auto-save to localStorage every 30 seconds
    clearTimeout(window.autoSaveTimer);
    window.autoSaveTimer = setTimeout(saveDraft, 30000);
}

function submitForm(event) {
    console.log('Submit function called!');
    event.preventDefault();
    
    console.log('Current step:', currentStep, 'Total steps:', totalSteps);
    
    if (!validateStep(currentStep)) {
        console.log('Validation failed for step', currentStep);
        return;
    }
    
    console.log('Validation passed, submitting form...');
    
    // Show loading overlay
    document.getElementById('loadingOverlay').classList.add('active');
    
    const formData = new FormData(document.getElementById('inspectionForm'));
    
    // Add progressively uploaded file paths
    for (const fieldName in uploadedFiles) {
        formData.append('existing_' + fieldName, uploadedFiles[fieldName]);
    }
    
    // Also check old savedFiles for backward compatibility
    const savedFiles = JSON.parse(localStorage.getItem('savedFiles') || '{}');
    for (const fieldName in savedFiles) {
        if (!uploadedFiles[fieldName]) {
            const fileInput = document.querySelector(`[name="${fieldName}"]`);
            // Only add existing file if no new file was uploaded
            if (fileInput && fileInput.type === 'file' && (!fileInput.files || fileInput.files.length === 0)) {
                formData.append('existing_' + fieldName, savedFiles[fieldName]);
            }
        }
    }
    
    console.log('Sending data to submit.php...');
    
    // Show progress message
    const loadingText = document.querySelector('#loadingOverlay p');
    if (loadingText) {
        loadingText.textContent = 'Processing images and generating PDF... This may take 2-3 minutes.';
    }
    
    fetch('submit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response received:', response.status);
        
        // Check if response is OK
        if (!response.ok) {
            throw new Error('Server returned ' + response.status + ': ' + response.statusText);
        }
        
        // Get response text first to handle errors
        return response.text().then(text => {
            console.log('Raw response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON. Response was:', text);
                throw new Error('Invalid JSON response from server. Check console for details.');
            }
        });
    })
    .then(data => {
        console.log('Response data:', data);
        document.getElementById('loadingOverlay').classList.remove('active');
        
        if (data.success) {
            console.log('Submission successful!');
            
            // Delete draft from server if exists
            const draftId = localStorage.getItem('draftId');
            if (draftId) {
                fetch('delete-draft.php?draft_id=' + draftId, {
                    method: 'DELETE'
                }).catch(err => console.log('Draft cleanup error:', err));
            }
            
            // Clear all draft data
            localStorage.removeItem('inspectionDraft');
            localStorage.removeItem('draftId');
            localStorage.removeItem('savedFiles');
            
            // Show success message
            document.getElementById('successMessage').classList.add('active');
        } else {
            console.error('Submission failed:', data.message);
            alert('Error: ' + (data.message || 'Submission failed'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        document.getElementById('loadingOverlay').classList.remove('active');
        alert('Error submitting form: ' + error.message);
    });
}


/**
 * Progressive Upload Functions
 * Upload images immediately when selected
 */
function setupProgressiveUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                uploadImageImmediately(input.name, file, input.id);
            }
        });
    });
    
    // Load previously uploaded files
    loadUploadedFiles();
}

function uploadImageImmediately(fieldName, file, inputId) {
    const previewId = inputId + 'Preview';
    const preview = document.getElementById(previewId);
    
    if (!preview) return;
    
    // Show uploading status
    preview.innerHTML = '<div class="uploading">📤 Uploading...</div>';
    
    // Create form data
    const formData = new FormData();
    formData.append('image', file);
    formData.append('field_name', fieldName);
    formData.append('current_step', currentStep || 1);
    
    if (draftId) {
        formData.append('draft_id', draftId);
    }
    
    // Upload via AJAX
    fetch('upload-image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store draft ID
            if (data.draft_id) {
                draftId = data.draft_id;
                localStorage.setItem('draftId', draftId);
            }
            
            // Store file path
            uploadedFiles[fieldName] = data.file_path;
            localStorage.setItem('uploadedFiles', JSON.stringify(uploadedFiles));
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="replace-image-btn" onclick="replaceImage('${fieldName}')">Replace Image</button>
                    <span class="upload-success">✅ Uploaded</span>
                `;
            };
            reader.readAsDataURL(file);
            
            console.log('Image uploaded:', fieldName, data.file_path);
        } else {
            preview.innerHTML = `<div class="upload-error">❌ Upload failed: ${data.message}</div>`;
            console.error('Upload failed:', data.message);
        }
    })
    .catch(error => {
        preview.innerHTML = `<div class="upload-error">❌ Upload error: ${error.message}</div>`;
        console.error('Upload error:', error);
    });
}

function loadUploadedFiles() {
    const savedFiles = localStorage.getItem('uploadedFiles');
    
    if (savedFiles) {
        try {
            uploadedFiles = JSON.parse(savedFiles);
            
            // Show previews for uploaded files
            for (let fieldName in uploadedFiles) {
                const filePath = uploadedFiles[fieldName];
                const fileInput = document.querySelector(`[name="${fieldName}"]`);
                
                if (fileInput && fileInput.type === 'file') {
                    const previewId = fileInput.id + 'Preview';
                    const preview = document.getElementById(previewId);
                    
                    if (preview) {
                        preview.innerHTML = `
                            <img src="${filePath}" alt="Saved image">
                            <button type="button" class="replace-image-btn" onclick="replaceImage('${fieldName}')">Replace Image</button>
                            <span class="upload-success">✅ Saved</span>
                        `;
                        
                        // Mark as not required since file exists
                        fileInput.removeAttribute('required');
                        fileInput.dataset.savedFile = filePath;
                    }
                }
            }
            
            console.log('Loaded', Object.keys(uploadedFiles).length, 'uploaded images');
        } catch (e) {
            console.error('Error loading uploaded files:', e);
        }
    }
}

// Add CSS for upload status
const uploadStyle = document.createElement('style');
uploadStyle.textContent = `
    .uploading {
        padding: 10px;
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 5px;
        text-align: center;
        animation: pulse 1s infinite;
    }
    
    .upload-success {
        display: inline-block;
        margin-top: 5px;
        padding: 5px 10px;
        background: #d4edda;
        color: #155724;
        border-radius: 3px;
        font-size: 12px;
    }
    
    .upload-error {
        padding: 10px;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        color: #721c24;
        text-align: center;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
`;
document.head.appendChild(uploadStyle);


// ============================================================================
// T-SUBMIT: Test PDF Generation for Current Progress
// ============================================================================
function testSubmit(event) {
    event.preventDefault();
    
    console.log('T-SUBMIT: Generating test PDF for steps 1-' + currentStep);
    
    // Show loading
    const tSubmitBtn = document.getElementById('tSubmitBtn');
    const originalText = tSubmitBtn.textContent;
    tSubmitBtn.textContent = '⏳ Generating PDF...';
    tSubmitBtn.disabled = true;
    
    // Collect all form data up to current step
    const form = document.getElementById('inspectionForm');
    const formData = new FormData();
    
    // Add current step info
    formData.append('test_mode', 'true');
    formData.append('current_step', currentStep);
    formData.append('total_steps', totalSteps);
    
    // Collect all form inputs
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        // Get the step number for this input
        const stepElement = input.closest('.form-step');
        const inputStep = stepElement ? parseInt(stepElement.getAttribute('data-step')) : 1;
        
        // Only include inputs from completed steps (up to current step)
        if (inputStep <= currentStep) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                if (input.checked) {
                    formData.append(input.name, input.value);
                }
            } else if (input.type === 'file') {
                // Skip file inputs - we'll use uploaded files
            } else if (input.name) {
                formData.append(input.name, input.value);
            }
        }
    });
    
    // Add progressively uploaded file paths
    for (const fieldName in uploadedFiles) {
        formData.append('existing_' + fieldName, uploadedFiles[fieldName]);
    }
    
    // Add saved files from localStorage
    const savedFiles = JSON.parse(localStorage.getItem('savedFiles') || '{}');
    for (const fieldName in savedFiles) {
        // Only add if not already added from uploadedFiles
        if (!uploadedFiles[fieldName]) {
            formData.append('existing_' + fieldName, savedFiles[fieldName]);
        }
    }
    
    console.log('T-SUBMIT: Sending data to t-submit.php...');
    
    // Send to test submit handler
    fetch('t-submit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        tSubmitBtn.textContent = originalText;
        tSubmitBtn.disabled = false;
        
        if (data.success) {
            console.log('T-SUBMIT: PDF generated successfully!', data.pdf_path);
            
            // Show success message with download link
            const message = `✅ Test PDF Generated Successfully!\n\n` +
                          `Steps Included: 1-${currentStep}\n` +
                          `PDF Path: ${data.pdf_path}\n\n` +
                          `Click OK to download the PDF.`;
            
            if (confirm(message)) {
                // Open PDF in new tab
                window.open(data.pdf_path, '_blank');
            }
            
            // Also show a temporary success banner
            showTestPdfBanner(data.pdf_path, currentStep);
        } else {
            console.error('T-SUBMIT: Error:', data.message);
            alert('❌ Test PDF Generation Failed:\n\n' + data.message);
        }
    })
    .catch(error => {
        tSubmitBtn.textContent = originalText;
        tSubmitBtn.disabled = false;
        
        console.error('T-SUBMIT: Fetch error:', error);
        alert('❌ Error generating test PDF:\n\n' + error.message);
    });
}

// Show temporary success banner for test PDF
function showTestPdfBanner(pdfPath, stepsIncluded) {
    // Remove existing banner if any
    const existingBanner = document.getElementById('testPdfBanner');
    if (existingBanner) {
        existingBanner.remove();
    }
    
    // Create banner
    const banner = document.createElement('div');
    banner.id = 'testPdfBanner';
    banner.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4CAF50;
        color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        z-index: 10000;
        max-width: 400px;
        animation: slideIn 0.3s ease-out;
    `;
    
    banner.innerHTML = `
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 32px;">✅</div>
            <div style="flex: 1;">
                <div style="font-weight: bold; font-size: 16px; margin-bottom: 5px;">
                    Test PDF Generated!
                </div>
                <div style="font-size: 14px; opacity: 0.9;">
                    Steps 1-${stepsIncluded} included
                </div>
                <a href="${pdfPath}" target="_blank" style="color: white; text-decoration: underline; font-size: 14px; display: inline-block; margin-top: 8px;">
                    📄 Open PDF
                </a>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; line-height: 1;">
                ×
            </button>
        </div>
    `;
    
    document.body.appendChild(banner);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (banner.parentElement) {
            banner.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => banner.remove(), 300);
        }
    }, 10000);
}

// Add CSS animation
if (!document.getElementById('testPdfStyles')) {
    const style = document.createElement('style');
    style.id = 'testPdfStyles';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}
