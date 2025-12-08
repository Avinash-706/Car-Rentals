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
    // Set current dat  e and time for Step 2
    updateDateTime();
    
    // Load draft if exists
    loadDraft();
    
    // Event listeners
    document.getElementById('nextBtn').addEventListener('click', nextStep);
    document.getElementById('prevBtn').addEventListener('click', prevStep);
    document.getElementById('saveDraftBtn').addEventListener('click', saveDraft);
    document.getElementById('discardDraftBtn').addEventListener('click', discardDraft);
    document.getElementById('fetchLocation').addEventListener('click', fetchLocation);
    
    // Submit button - both form submit and direct click
    document.getElementById('inspectionForm').addEventListener('submit', submitForm);
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        e.preventDefault();
        submitForm(e);
    });
    
    // File upload preview
    document.getElementById('carPhoto').addEventListener('change', previewImage);
    
    // Setup camera capture for all file inputs
    setupCameraCapture();
    
    // Setup all image previews
    setupImagePreviews();
    
    // Setup OK checkbox logic
    setupOkCheckboxLogic();
    
    // Setup payment toggle logic
    setupPaymentToggle();
    
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
            
            // Debug logging for Step 5
            if (step === 5) {
                console.log('Validating file input:', field.name, {
                    hasSavedFile: hasSavedFile,
                    hasNewFile: hasNewFile,
                    filesLength: field.files ? field.files.length : 0
                });
            }
            
            if (!hasSavedFile && !hasNewFile) {
                field.focus();
                alert('Please upload all required images for: ' + field.name);
                return false;
            }
            
            // Only validate new uploads
            if (hasNewFile) {
                // Validate file size (15MB max)
                if (field.files[0].size > 15728640) {
                    alert('File size must be less than 15MB');
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
    const locationBtn = document.getElementById('fetchLocation');
    errorDiv.textContent = '';
    errorDiv.style.display = 'none';
    
    // Check if geolocation is supported
    if (!navigator.geolocation) {
        alert("Your device does not support Geolocation.");
        return;
    }
    
    // Show loading state
    locationBtn.textContent = '⏳';
    locationBtn.disabled = true;
    
    // Immediately request location - this triggers the native browser permission popup
    navigator.geolocation.getCurrentPosition(
        // SUCCESS callback
        function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            const accuracy = position.coords.accuracy;
            
            // Auto-fill latitude and longitude
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lon.toFixed(6);
            
            // Show success message
            showLocationSuccess(`✅ Location captured (±${Math.round(accuracy)}m accuracy)`, errorDiv);
            
            // Fetch address via reverse geocoding
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`, {
                headers: { 'User-Agent': 'CarInspectionApp/1.0' }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('locationAddress').value = data.display_name || 'Address not found';
                locationBtn.textContent = '✓';
                locationBtn.style.background = '#4CAF50';
                setTimeout(() => {
                    locationBtn.textContent = '📍';
                    locationBtn.style.background = '';
                    locationBtn.disabled = false;
                }, 2000);
            })
            .catch(error => {
                document.getElementById('locationAddress').value = `Lat: ${lat}, Lon: ${lon}`;
                locationBtn.textContent = '📍';
                locationBtn.disabled = false;
            });
        },
        // ERROR callback - Force permission popup handling
        function(error) {
            locationBtn.textContent = '📍';
            locationBtn.disabled = false;
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("Please enable Location Permission for this site.\n\n" +
                          "📱 Android: Tap the lock icon → Permissions → Location → Allow\n" +
                          "🍎 iOS: Tap 'AA' → Website Settings → Location → Allow");
                    showPermissionDeniedPopup();
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location unavailable. Please turn ON GPS and ensure Location Services are enabled.");
                    break;
                case error.TIMEOUT:
                    alert("Location request timed out. Please check your internet connection and try again.");
                    break;
                default:
                    alert("Unable to fetch location. Please try again.");
            }
        },
        // OPTIONS - High accuracy, no cache
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}



function showLocationError(message, errorDiv) {
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    errorDiv.style.background = '#ffebee';
    errorDiv.style.color = '#c62828';
    errorDiv.style.padding = '15px';
    errorDiv.style.borderRadius = '4px';
    errorDiv.style.marginTop = '10px';
    errorDiv.style.whiteSpace = 'pre-line';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.border = '1px solid #ef5350';
}

function showLocationSuccess(message, errorDiv) {
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    errorDiv.style.background = '#e8f5e9';
    errorDiv.style.color = '#2e7d32';
    errorDiv.style.padding = '10px';
    errorDiv.style.borderRadius = '4px';
    errorDiv.style.marginTop = '10px';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.border = '1px solid #66bb6a';
    
    // Auto-hide success message after 3 seconds
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 3000);
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
                if (file.size > 15728640) {
                    alert('File size must be less than 15MB');
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
    // Handle OK logic (Steps 6 & 7) - Show image when NOT OK
    const okGroups = document.querySelectorAll('[data-ok-group]');
    
    okGroups.forEach(group => {
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const okCheckbox = group.querySelector('[data-ok-checkbox]');
        
        if (!okCheckbox) return;
        
        setupConditionalImageLogic(group, checkboxes, okCheckbox, 'ok');
    });
    
    // Handle "Not Checked" logic (Step 8) - Show image when NOT "Not Checked"
    const notCheckedGroups = document.querySelectorAll('[data-not-checked-group]');
    
    notCheckedGroups.forEach(group => {
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const notCheckedCheckbox = group.querySelector('[data-not-checked-checkbox]');
        
        if (!notCheckedCheckbox) return;
        
        setupConditionalImageLogic(group, checkboxes, notCheckedCheckbox, 'not checked');
    });
}

function setupConditionalImageLogic(group, checkboxes, hideCheckbox, hideValue) {
    // Get conditional image field if exists
    const conditionalImageId = group.getAttribute('data-conditional-image');
    const imageGroup = conditionalImageId ? document.getElementById(conditionalImageId + '_group') : null;
    
    // Convert snake_case to camelCase for input ID
    let imageInputId = null;
    if (conditionalImageId) {
        const parts = conditionalImageId.split('_');
        imageInputId = parts[0] + parts.slice(1).map(p => p.charAt(0).toUpperCase() + p.slice(1)).join('');
    }
    const imageInput = imageInputId ? document.getElementById(imageInputId) : null;
    
    // Debug logging
    if (conditionalImageId) {
        console.log('Conditional image setup:', {
            conditionalImageId: conditionalImageId,
            imageInputId: imageInputId,
            imageGroupFound: !!imageGroup,
            imageInputFound: !!imageInput,
            hideValue: hideValue
        });
    }
    
    // Function to check if hide value is selected (case-insensitive)
    function isHideValueSelected() {
        const checkedValues = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value.toLowerCase());
        return checkedValues.includes(hideValue.toLowerCase());
    }
    
    // Function to toggle conditional image field
    function toggleConditionalImage() {
        if (!imageGroup || !imageInput) return;
        
        const hideSelected = isHideValueSelected();
        
        if (hideSelected) {
            // Hide image field and remove required
            imageGroup.style.display = 'none';
            imageInput.removeAttribute('required');
            imageInput.value = ''; // Clear file input
            // Clear preview
            const previewId = imageInput.id + 'Preview';
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.innerHTML = '';
            }
            // Remove saved file marker
            delete imageInput.dataset.savedFile;
        } else {
            // Show image field and make required if any checkbox is checked
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (anyChecked) {
                imageGroup.style.display = 'block';
                imageInput.setAttribute('required', 'required');
            }
        }
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this === hideCheckbox) {
                // If hide checkbox is checked, uncheck all others
                if (this.checked) {
                    checkboxes.forEach(cb => {
                        if (cb !== hideCheckbox) {
                            cb.checked = false;
                        }
                    });
                }
            } else {
                // If any other checkbox is checked, uncheck hide checkbox
                if (this.checked) {
                    hideCheckbox.checked = false;
                }
            }
            
            // Toggle conditional image field
            toggleConditionalImage();
        });
    });
    
    // Initial check on page load
    toggleConditionalImage();
}

function setupPaymentToggle() {
    const paymentYes = document.getElementById('payment_yes');
    const paymentNo = document.getElementById('payment_no');
    const paymentDetailsSection = document.getElementById('payment_details_section');
    const paymentScreenshot = document.getElementById('payment_screenshot');
    
    if (!paymentYes || !paymentNo || !paymentDetailsSection) return;
    
    // Function to toggle payment details
    function togglePaymentDetails() {
        if (paymentYes.checked) {
            paymentDetailsSection.style.display = 'block';
            // Make payment screenshot required when Yes is selected
            if (paymentScreenshot) {
                paymentScreenshot.setAttribute('required', 'required');
            }
        } else {
            paymentDetailsSection.style.display = 'none';
            // Remove required when No is selected
            if (paymentScreenshot) {
                paymentScreenshot.removeAttribute('required');
            }
        }
    }
    
    // Add event listeners
    paymentYes.addEventListener('change', togglePaymentDetails);
    paymentNo.addEventListener('change', togglePaymentDetails);
    
    // Check initial state (for draft loading)
    togglePaymentDetails();
}

function saveDraft() {
    console.log('Saving draft...');
    const form = document.getElementById('inspectionForm');
    
    // Get the current draft ID
    const currentDraftId = localStorage.getItem('draftId') || draftId || '';
    
    // Collect uploaded files from both global variable and localStorage
    let allUploadedFiles = {};
    
    // First, get from localStorage
    const storedFiles = localStorage.getItem('uploadedFiles');
    if (storedFiles) {
        try {
            allUploadedFiles = JSON.parse(storedFiles);
        } catch (e) {
            console.error('Error parsing uploadedFiles from localStorage:', e);
        }
    }
    
    // Then merge with global uploadedFiles (in case of new uploads)
    if (uploadedFiles && typeof uploadedFiles === 'object') {
        allUploadedFiles = { ...allUploadedFiles, ...uploadedFiles };
    }
    
    console.log('Uploaded files to save:', allUploadedFiles);
    
    // Collect all form data as JSON for better structure
    const draftData = {
        current_step: currentStep,
        draft_id: currentDraftId,
        form_data: {},
        uploaded_files: allUploadedFiles
    };
    
    // Collect all form fields with proper handling for arrays
    const inputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
    const fieldGroups = {};
    
    inputs.forEach(input => {
        if (!input.name) return;
        
        if (input.type === 'checkbox') {
            // Handle checkbox arrays
            if (!fieldGroups[input.name]) {
                fieldGroups[input.name] = [];
            }
            if (input.checked) {
                fieldGroups[input.name].push(input.value);
            }
        } else if (input.type === 'radio') {
            // Handle radio buttons
            if (input.checked) {
                draftData.form_data[input.name] = input.value;
            }
        } else if (input.tagName === 'SELECT' && input.multiple) {
            // Handle multi-select
            const selected = Array.from(input.selectedOptions).map(opt => opt.value);
            draftData.form_data[input.name] = selected;
        } else {
            // Handle regular inputs
            draftData.form_data[input.name] = input.value;
        }
    });
    
    // Add checkbox arrays to form_data
    for (const fieldName in fieldGroups) {
        if (fieldGroups[fieldName].length > 0) {
            draftData.form_data[fieldName] = fieldGroups[fieldName];
        }
    }
    
    console.log('Draft data to save:', draftData);
    
    // Show loading indicator
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const originalText = saveDraftBtn.textContent;
    saveDraftBtn.textContent = 'Saving...';
    saveDraftBtn.disabled = true;
    
    fetch('save-draft.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(draftData)
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
            draftId = data.draft_id;
            localStorage.setItem('draftId', data.draft_id);
            
            // Update uploaded files from server response
            if (data.draft_data && data.draft_data.uploaded_files) {
                uploadedFiles = data.draft_data.uploaded_files;
                localStorage.setItem('uploadedFiles', JSON.stringify(data.draft_data.uploaded_files));
                localStorage.setItem('savedFiles', JSON.stringify(data.draft_data.uploaded_files));
                console.log('Updated uploadedFiles:', uploadedFiles);
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
    const storedDraftId = localStorage.getItem('draftId');
    
    if (!storedDraftId) {
        console.log('No draft ID found');
        // Try to load from localStorage as fallback
        const storedFiles = localStorage.getItem('uploadedFiles');
        if (storedFiles) {
            try {
                uploadedFiles = JSON.parse(storedFiles);
                console.log('Loaded uploadedFiles from localStorage:', uploadedFiles);
            } catch (e) {
                console.error('Error parsing uploadedFiles:', e);
            }
        }
        return;
    }
    
    // Update global draftId
    draftId = storedDraftId;
    
    // Fetch draft from server
    fetch('load-draft.php?draft_id=' + storedDraftId)
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
            
            // MIGRATION: Handle old field names from previous versions
            if (draftData.form_data) {
                // Migrate expert_id → engineer_name
                if (draftData.form_data.expert_id && !draftData.form_data.engineer_name) {
                    draftData.form_data.engineer_name = draftData.form_data.expert_id;
                    delete draftData.form_data.expert_id;
                    console.log('Migrated: expert_id → engineer_name');
                }
                
                // Remove deprecated fields (they won't be restored)
                const deprecatedFields = [
                    'inspection_date',
                    'pending_amount', 
                    'inspection_delayed',
                    'car_registered_city',
                    'car_purchase_invoice',
                    'bifuel_certification'
                ];
                
                deprecatedFields.forEach(field => {
                    if (draftData.form_data[field]) {
                        delete draftData.form_data[field];
                        console.log('Removed deprecated field:', field);
                    }
                });
            }
            
            // Restore current step
            if (draftData.current_step) {
                currentStep = parseInt(draftData.current_step);
            }
            
            // Restore form fields
            if (draftData.form_data) {
                for (let key in draftData.form_data) {
                    const value = draftData.form_data[key];
                    const fields = document.querySelectorAll(`[name="${key}"]`);
                    
                    if (fields.length === 0) continue;
                    
                    const firstField = fields[0];
                    
                    if (firstField.type === 'checkbox') {
                        // Handle checkbox arrays
                        const values = Array.isArray(value) ? value : [value];
                        fields.forEach(field => {
                            field.checked = values.includes(field.value);
                        });
                    } else if (firstField.type === 'radio') {
                        // Handle radio buttons
                        fields.forEach(field => {
                            field.checked = field.value === value;
                        });
                    } else if (firstField.tagName === 'SELECT' && firstField.multiple) {
                        // Handle multi-select
                        const values = Array.isArray(value) ? value : [value];
                        Array.from(firstField.options).forEach(option => {
                            option.selected = values.includes(option.value);
                        });
                    } else {
                        // Handle regular inputs
                        firstField.value = value;
                    }
                }
            }
            
            // Restore uploaded images
            if (draftData.uploaded_files && Object.keys(draftData.uploaded_files).length > 0) {
                console.log('Restoring uploaded files:', draftData.uploaded_files);
                
                // Store in both uploadedFiles and localStorage
                uploadedFiles = draftData.uploaded_files;
                localStorage.setItem('uploadedFiles', JSON.stringify(draftData.uploaded_files));
                localStorage.setItem('savedFiles', JSON.stringify(draftData.uploaded_files));
                
                let loadedCount = 0;
                let failedCount = 0;
                
                for (let fieldName in draftData.uploaded_files) {
                    const filePath = draftData.uploaded_files[fieldName];
                    const fileInput = document.querySelector(`[name="${fieldName}"]`);
                    
                    if (fileInput && fileInput.type === 'file') {
                        const previewId = fileInput.id + 'Preview';
                        const preview = document.getElementById(previewId);
                        
                        if (preview) {
                            // Verify image exists before showing preview
                            const img = new Image();
                            img.onload = function() {
                                // Image loaded successfully
                                preview.innerHTML = `
                                    <img src="${filePath}" alt="Saved image">
                                    <button type="button" class="replace-image-btn" onclick="replaceImage('${fieldName}')">Replace Image</button>
                                    <span class="upload-success">✅ Uploaded</span>
                                `;
                                
                                // Mark field as having saved file
                                fileInput.dataset.savedFile = filePath;
                                fileInput.removeAttribute('required');
                                loadedCount++;
                                console.log('✅ Loaded image:', fieldName, filePath);
                            };
                            img.onerror = function() {
                                // Image failed to load
                                console.error('❌ Failed to load image:', fieldName, filePath);
                                preview.innerHTML = `
                                    <div class="upload-error">❌ Image not found. Please upload again.</div>
                                `;
                                
                                // Remove from uploadedFiles
                                delete uploadedFiles[fieldName];
                                localStorage.setItem('uploadedFiles', JSON.stringify(uploadedFiles));
                                
                                // Make field required again
                                fileInput.setAttribute('required', 'required');
                                delete fileInput.dataset.savedFile;
                                failedCount++;
                            };
                            img.src = filePath;
                        }
                    }
                }
                
                // Show summary after a short delay to let images load
                setTimeout(() => {
                    console.log(`Restored ${loadedCount} images, ${failedCount} failed`);
                    if (failedCount > 0) {
                        alert(`Draft loaded! ${loadedCount} images restored.\n${failedCount} images could not be found and need to be re-uploaded.`);
                    }
                }, 1000);
            }
            
            showStep(currentStep);
            
            // Trigger payment toggle check after draft loads
            setTimeout(() => {
                const paymentYes = document.getElementById('payment_yes');
                const paymentNo = document.getElementById('payment_no');
                if (paymentYes && paymentYes.checked) {
                    document.getElementById('payment_details_section').style.display = 'block';
                    const paymentScreenshot = document.getElementById('payment_screenshot');
                    if (paymentScreenshot && !paymentScreenshot.dataset.savedFile) {
                        paymentScreenshot.setAttribute('required', 'required');
                    }
                } else if (paymentNo && paymentNo.checked) {
                    document.getElementById('payment_details_section').style.display = 'none';
                }
                
                // Trigger conditional image fields after draft loads
                // Handle OK groups (Steps 6 & 7)
                const okGroups = document.querySelectorAll('[data-ok-group][data-conditional-image]');
                okGroups.forEach(group => {
                    const conditionalImageId = group.getAttribute('data-conditional-image');
                    const imageGroup = document.getElementById(conditionalImageId + '_group');
                    
                    if (!imageGroup) return;
                    
                    // Check if OK is selected
                    const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                    const checkedValues = Array.from(checkboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value.toLowerCase());
                    const okSelected = checkedValues.includes('ok');
                    
                    // Show/hide image field based on selection
                    if (okSelected) {
                        imageGroup.style.display = 'none';
                    } else if (checkedValues.length > 0) {
                        imageGroup.style.display = 'block';
                        // Get the image input
                        const parts = conditionalImageId.split('_');
                        const imageInputId = parts[0] + parts.slice(1).map(p => p.charAt(0).toUpperCase() + p.slice(1)).join('');
                        const imageInput = document.getElementById(imageInputId);
                        if (imageInput && !imageInput.dataset.savedFile) {
                            imageInput.setAttribute('required', 'required');
                        }
                    }
                });
                
                // Handle "Not Checked" groups (Step 8)
                const notCheckedGroups = document.querySelectorAll('[data-not-checked-group][data-conditional-image]');
                notCheckedGroups.forEach(group => {
                    const conditionalImageId = group.getAttribute('data-conditional-image');
                    const imageGroup = document.getElementById(conditionalImageId + '_group');
                    
                    if (!imageGroup) return;
                    
                    // Check if "Not Checked" is selected
                    const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                    const checkedValues = Array.from(checkboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value.toLowerCase());
                    const notCheckedSelected = checkedValues.includes('not checked');
                    
                    // Show/hide image field based on selection
                    if (notCheckedSelected) {
                        imageGroup.style.display = 'none';
                    } else if (checkedValues.length > 0) {
                        imageGroup.style.display = 'block';
                        // Get the image input
                        const parts = conditionalImageId.split('_');
                        const imageInputId = parts[0] + parts.slice(1).map(p => p.charAt(0).toUpperCase() + p.slice(1)).join('');
                        const imageInput = document.getElementById(imageInputId);
                        if (imageInput && !imageInput.dataset.savedFile) {
                            imageInput.setAttribute('required', 'required');
                        }
                    }
                });
            }, 100);
            
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
    
    // Add draft_id for cleanup after successful submission
    const draftId = localStorage.getItem('draftId');
    if (draftId) {
        formData.append('draft_id', draftId);
    }
    
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
            
            // CRITICAL FIX: Mark the input as having a saved file
            const fileInput = document.querySelector(`[name="${fieldName}"]`);
            if (fileInput) {
                fileInput.dataset.savedFile = data.file_path;
                fileInput.removeAttribute('required');
            }
            
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
    // Don't load from localStorage if we have a draft ID
    // The loadDraft() function will handle loading images from the server
    const draftId = localStorage.getItem('draftId');
    if (draftId) {
        console.log('Draft ID exists, skipping localStorage image load (will load from server)');
        return;
    }
    
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
                        // Verify image exists by trying to load it
                        const img = new Image();
                        img.onload = function() {
                            preview.innerHTML = `
                                <img src="${filePath}" alt="Saved image">
                                <button type="button" class="replace-image-btn" onclick="replaceImage('${fieldName}')">Replace Image</button>
                                <span class="upload-success">✅ Saved</span>
                            `;
                            
                            // Mark as not required since file exists
                            fileInput.removeAttribute('required');
                            fileInput.dataset.savedFile = filePath;
                        };
                        img.onerror = function() {
                            console.warn('Image not found:', filePath);
                            // Remove from uploadedFiles if image doesn't exist
                            delete uploadedFiles[fieldName];
                            localStorage.setItem('uploadedFiles', JSON.stringify(uploadedFiles));
                        };
                        img.src = filePath;
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


// Setup camera capture for all file inputs
function setupCameraCapture() {
    // Find all file inputs that accept images
    const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    fileInputs.forEach(fileInput => {
        const fileUploadDiv = fileInput.closest('.file-upload');
        if (!fileUploadDiv) return;
        
        const fileLabel = fileUploadDiv.querySelector('.file-label');
        if (!fileLabel) return;
        
        // Check if camera button already exists
        if (fileUploadDiv.querySelector('.camera-btn-wrapper')) return;
        
        // Create camera input
        const cameraInput = document.createElement('input');
        cameraInput.type = 'file';
        cameraInput.accept = 'image/*';
        cameraInput.capture = 'environment';
        cameraInput.style.display = 'none';
        cameraInput.id = fileInput.id + 'Camera';
        
        // Create camera button label
        const cameraLabel = document.createElement('label');
        cameraLabel.htmlFor = cameraInput.id;
        cameraLabel.className = 'file-label';
        cameraLabel.style.flex = '1';
        cameraLabel.style.background = '#848383ff';
        cameraLabel.innerHTML = '<span class="camera-icon">📷</span><span class="file-text">Take Photo</span>';
        
        // Create wrapper for both buttons
        const wrapper = document.createElement('div');
        wrapper.className = 'camera-btn-wrapper';
        wrapper.style.display = 'flex';
        wrapper.style.gap = '10px';
        
        // Hide original input
        fileInput.style.display = 'none';
        
        // Update original label
        fileLabel.style.flex = '1';
        fileLabel.querySelector('.camera-icon').textContent = '📁';
        
        // Insert camera input and reorganize
        fileUploadDiv.insertBefore(cameraInput, fileLabel);
        wrapper.appendChild(fileLabel);
        wrapper.appendChild(cameraLabel);
        fileUploadDiv.insertBefore(wrapper, fileUploadDiv.querySelector('.file-preview'));
        
        // Handle camera input change
        cameraInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
}

// Show permission denied popup with instructions
function showPermissionDeniedPopup() {
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    const isAndroid = /Android/i.test(navigator.userAgent);
    const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
    
    const modal = document.createElement('div');
    modal.id = 'locationPermissionModal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.85);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.3s;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 16px;
        max-width: 450px;
        width: 100%;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        animation: slideUp 0.3s;
    `;
    
    let instructions = '';
    if (isAndroid) {
        instructions = `
            <div style="background: #e8f5e9; padding: 20px; border-radius: 12px; margin: 20px 0; border-left: 4px solid #4CAF50;">
                <h3 style="margin: 0 0 15px 0; color: #2e7d32; font-size: 16px;">📱 Android Instructions:</h3>
                <ol style="margin: 0; padding-left: 20px; color: #555; line-height: 1.8;">
                    <li>Tap the <strong>🔒 lock icon</strong> in the address bar</li>
                    <li>Tap <strong>"Permissions"</strong> or <strong>"Site settings"</strong></li>
                    <li>Find <strong>"Location"</strong></li>
                    <li>Change to <strong>"Allow"</strong></li>
                    <li>Refresh this page</li>
                    <li>Tap <strong>"Get Location"</strong> again</li>
                </ol>
            </div>
            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-top: 15px;">
                <strong style="color: #e65100;">Alternative:</strong>
                <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">
                    Settings → Apps → Browser → Permissions → Location → Allow
                </p>
            </div>
        `;
    } else if (isIOS) {
        instructions = `
            <div style="background: #e3f2fd; padding: 20px; border-radius: 12px; margin: 20px 0; border-left: 4px solid #2196F3;">
                <h3 style="margin: 0 0 15px 0; color: #1565c0; font-size: 16px;">🍎 iOS Instructions:</h3>
                <ol style="margin: 0; padding-left: 20px; color: #555; line-height: 1.8;">
                    <li>Tap <strong>"AA"</strong> in the address bar</li>
                    <li>Tap <strong>"Website Settings"</strong></li>
                    <li>Find <strong>"Location"</strong></li>
                    <li>Change to <strong>"Allow"</strong></li>
                    <li>Refresh this page</li>
                    <li>Tap <strong>"Get Location"</strong> again</li>
                </ol>
            </div>
            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-top: 15px;">
                <strong style="color: #e65100;">Alternative:</strong>
                <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">
                    Settings → Privacy → Location Services → Safari → While Using the App
                </p>
            </div>
        `;
    } else {
        instructions = `
            <div style="background: #e8f5e9; padding: 20px; border-radius: 12px; margin: 20px 0; border-left: 4px solid #4CAF50;">
                <h3 style="margin: 0 0 15px 0; color: #2e7d32; font-size: 16px;">💻 Desktop Instructions:</h3>
                <ol style="margin: 0; padding-left: 20px; color: #555; line-height: 1.8;">
                    <li>Click the <strong>🔒 lock icon</strong> in the address bar</li>
                    <li>Find <strong>"Location"</strong> permission</li>
                    <li>Change to <strong>"Allow"</strong></li>
                    <li>Refresh this page</li>
                    <li>Click <strong>"Get Location"</strong> again</li>
                </ol>
            </div>
        `;
    }
    
    content.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 48px; margin-bottom: 10px;">🚫</div>
            <h2 style="margin: 0; color: #d32f2f; font-size: 22px;">Location Permission Denied</h2>
        </div>
        <p style="margin: 0 0 10px 0; color: #666; text-align: center; line-height: 1.6;">
            You need to allow location access for this feature to work.
        </p>
        ${instructions}
        <button id="closePermissionModal" style="
            width: 100%;
            padding: 16px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        " onmouseover="this.style.background='#1976D2'" onmouseout="this.style.background='#2196F3'">
            Got It, I'll Enable Location
        </button>
    `;
    
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    document.getElementById('closePermissionModal').addEventListener('click', function() {
        document.body.removeChild(modal);
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

// Show location unavailable popup
function showLocationUnavailablePopup() {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.85);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 16px;
        max-width: 450px;
        width: 100%;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    `;
    
    content.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 48px; margin-bottom: 10px;">📡</div>
            <h2 style="margin: 0; color: #ff9800; font-size: 22px;">Location Unavailable</h2>
        </div>
        <p style="margin: 0 0 20px 0; color: #666; text-align: center; line-height: 1.6;">
            Unable to determine your location. Please check the following:
        </p>
        <div style="background: #fff3e0; padding: 20px; border-radius: 12px; border-left: 4px solid #ff9800;">
            <ul style="margin: 0; padding-left: 20px; color: #555; line-height: 1.8;">
                <li>Make sure <strong>Location Services</strong> are turned ON</li>
                <li>Check if you're in <strong>Airplane Mode</strong> (turn it OFF)</li>
                <li>Move to an area with <strong>better GPS signal</strong></li>
                <li>Try going <strong>outdoors</strong> for better accuracy</li>
                <li>Restart your device if the problem persists</li>
            </ul>
        </div>
        <button id="closeUnavailableModal" style="
            width: 100%;
            padding: 16px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        ">Try Again</button>
    `;
    
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    document.getElementById('closeUnavailableModal').addEventListener('click', function() {
        document.body.removeChild(modal);
        fetchLocation();
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

// Show location timeout popup
function showLocationTimeoutPopup() {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.85);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 16px;
        max-width: 450px;
        width: 100%;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    `;
    
    content.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 48px; margin-bottom: 10px;">⏱️</div>
            <h2 style="margin: 0; color: #ff5722; font-size: 22px;">Location Request Timed Out</h2>
        </div>
        <p style="margin: 0 0 20px 0; color: #666; text-align: center; line-height: 1.6;">
            It's taking too long to get your location. Please check:
        </p>
        <div style="background: #ffebee; padding: 20px; border-radius: 12px; border-left: 4px solid #ff5722;">
            <ul style="margin: 0; padding-left: 20px; color: #555; line-height: 1.8;">
                <li>Check your <strong>internet connection</strong></li>
                <li>Make sure <strong>GPS is enabled</strong></li>
                <li>Move to an <strong>open area</strong> for better signal</li>
                <li>Wait a moment and try again</li>
            </ul>
        </div>
        <button id="closeTimeoutModal" style="
            width: 100%;
            padding: 16px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        ">Try Again</button>
    `;
    
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    document.getElementById('closeTimeoutModal').addEventListener('click', function() {
        document.body.removeChild(modal);
        fetchLocation();
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

// Add CSS animations
if (!document.getElementById('locationModalStyles')) {
    const style = document.createElement('style');
    style.id = 'locationModalStyles';
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}
