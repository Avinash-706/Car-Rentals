# OTHER IMAGES - TROUBLESHOOTING GUIDE

## Issue
Selected images not showing in preview and count not updating.

## Root Cause
JavaScript handler may not be loading or initializing properly.

## Testing Steps

### 1. Test with Inline Script
Open `test-other-images-inline.html` in browser:
- Click "Select Images" button
- Choose 1-3 images
- **Expected:** Preview grid shows images, count updates

### 2. Check Browser Console
Open browser DevTools (F12) and check Console tab:
- Look for: "DOM loaded, initializing..."
- Look for: "Other images input found, adding listener"
- Look for: "Files selected: X"
- Look for any errors

### 3. Verify Elements Exist
In browser console, run:
```javascript
document.getElementById('otherImagesInput')
document.getElementById('otherImagesPreview')
document.getElementById('otherImagesCount')
```
All should return elements, not null.

## Quick Fixes

### Fix 1: Clear Browser Cache
1. Press Ctrl+Shift+Delete
2. Clear cached images and files
3. Reload page (Ctrl+F5)

### Fix 2: Check script.js Loading
In browser console:
```javascript
typeof initializeOtherImagesUpload
```
Should return "function", not "undefined"

### Fix 3: Manual Initialization
If function exists but not running, manually call in console:
```javascript
initializeOtherImagesUpload()
```

### Fix 4: Verify File Input
Check if input has correct attributes:
```javascript
const input = document.getElementById('otherImagesInput');
console.log('Multiple:', input.multiple);
console.log('Accept:', input.accept);
console.log('Max files:', input.dataset.maxFiles);
```

## Common Issues

### Issue: "initializeOtherImagesUpload is not defined"
**Solution:** script.js not loaded or code not appended
- Check if script.js is included: `<script src="script.js"></script>`
- Check if code exists at end of script.js
- Try test-other-images-inline.html which has inline code

### Issue: Input opens but nothing happens
**Solution:** Event listener not attached
- Check console for errors
- Manually attach: `document.getElementById('otherImagesInput').addEventListener('change', handleOtherImagesSelection)`

### Issue: Preview container not found
**Solution:** Element ID mismatch
- Verify: `<div id="otherImagesPreview">`
- Check for typos in ID

### Issue: Count not updating
**Solution:** Element missing or function not called
- Verify: `<span id="otherImagesCount">`
- Check if `updateOtherImagesCount()` is called

## Verification Checklist

- [ ] test-other-images-inline.html works (proves CSS/HTML correct)
- [ ] Browser console shows no errors
- [ ] All element IDs exist in DOM
- [ ] script.js is loaded (check Network tab)
- [ ] Functions are defined (check console)
- [ ] Event listener is attached
- [ ] File selection triggers handler
- [ ] Preview grid updates
- [ ] Count updates

## Manual Testing Script

Paste this in browser console to test manually:

```javascript
// Test 1: Check elements
console.log('Input:', document.getElementById('otherImagesInput'));
console.log('Preview:', document.getElementById('otherImagesPreview'));
console.log('Count:', document.getElementById('otherImagesCount'));

// Test 2: Check functions
console.log('Init function:', typeof initializeOtherImagesUpload);
console.log('Handle function:', typeof handleOtherImagesSelection);
console.log('Process function:', typeof processOtherImages);

// Test 3: Check arrays
console.log('Files array:', otherImagesFiles);
console.log('Uploaded array:', otherImagesUploaded);

// Test 4: Manual trigger
const input = document.getElementById('otherImagesInput');
if (input) {
    input.click(); // Should open file picker
}
```

## If Still Not Working

### Option 1: Use Inline Script
Copy the script from `test-other-images-inline.html` and paste it directly in index.php before `</body>`:

```html
<script>
// Paste the entire script here
</script>
</body>
```

### Option 2: Create Separate File
Create `other-images.js`:
```javascript
// Copy all the other images code here
```

Then include in index.php:
```html
<script src="script.js"></script>
<script src="other-images.js"></script>
```

### Option 3: Debug Mode
Add this at the start of script.js:
```javascript
console.log('script.js loaded');
window.addEventListener('load', function() {
    console.log('Window loaded');
    console.log('Other images input exists:', !!document.getElementById('otherImagesInput'));
});
```

## Success Indicators

When working correctly, you should see:
1. ✅ File input opens when clicking buttons
2. ✅ Selected files appear in preview grid
3. ✅ Count updates: "(X selected)"
4. ✅ Remove buttons (×) work
5. ✅ Console shows: "Files selected: X"
6. ✅ No errors in console

## Contact Points

If issue persists:
1. Check test-other-images-inline.html first
2. Compare with working inline version
3. Check browser console for specific errors
4. Verify all IDs match between HTML and JavaScript
5. Ensure script.js is actually loading (Network tab)
